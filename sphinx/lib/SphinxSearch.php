<?php

class SphinxSearch
{
    private $_sphinxConnectionString;
    /**
     * @var PDO
     */
    private $_sphinxDbh;

    private $_mysqlConnectionString;
    private $_mysqlUser;
    private $_mysqlPassword;
    /**
     * @var PDO
     */
    private $_mysqlDbh;

    public $sphinxQuery = '';

    function __construct($sphinxConnectionString, $mysqlConnectionString, $mysqlUser, $mysqlPassword) {
        $this->_sphinxConnectionString = $sphinxConnectionString;
        $this->_mysqlConnectionString = $mysqlConnectionString;
        $this->_mysqlUser = $mysqlUser;
        $this->_mysqlPassword = $mysqlPassword;
    }

    function __destruct() {
        $this->_mysqlDbh = null;
        $this->_sphinxDbh = null;
    }

    function connect() {
        try {
            $this->_sphinxDbh = new PDO($this->_sphinxConnectionString);
            $this->_mysqlDbh = new PDO($this->_mysqlConnectionString, $this->_mysqlUser, $this->_mysqlPassword);
            $this->_mysqlDbh->query('SET NAMES utf8');
        } catch(Exception $e) {
            print_r($e);
            die();
        }
    }

    function reindex($index=false) {
        $cmd = "/usr/bin/indexer --rotate " . ($index ? $index : "--all");
        exec($cmd);
    }

    function search($query, $appendTitles=false) {
        $sql = "
            SELECT id, WEIGHT() AS weight, sort, catsort1, catsort2, catsort3, catsort4, ex
            FROM products
            WHERE MATCH(:query)
            ORDER BY WEIGHT() DESC, catsort4 ASC, catsort3 ASC, catsort2 ASC, catsort1 ASC
            LIMIT 1000
            OPTION
                ranker=expr('sum(word_count*user_weight-min_hit_pos*10-ex)'),
                field_weights=(name=200,catname4=190,catname3=180,catname2=170,catname1=160)
        ";
        $sth = $this->_sphinxDbh->prepare($sql);
        $sth->bindValue(':query', $this->_getSphinxQuery($query), PDO::PARAM_STR);
        $sth->execute();

        $results = array();
        while($result = $sth->fetch(PDO::FETCH_ASSOC)) {
            $results[$result['id']] = $result;
        }

        if (!empty($results) && $appendTitles) return $this->_appendTitles($results);

        if(empty($results) && !isset($this->isHack)) return $this->_hack($query, $appendTitles);

        return $results;
    }

    private function _hack($query, $appendTitles) {
        $this->isHack = true;
        $re = '/(?<d>[\d]){1,2}(?<s>[\w]){1,2}/mi';
        if (preg_match_all($re, $query, $matches, PREG_SET_ORDER, 0) && count($matches) > 0) {
            $target = $matches[0][0];
            $replace = $matches[0]['d'].' '.$matches[0]['s'];
            $query = str_ireplace($target, $replace, $query);
        }
        return $this->search($query, $appendTitles);
    }

    private function _appendTitles($products) {
        $ids = array_column($products, 'id');

        $sth = $this->_mysqlDbh->query("
            SELECT id, name, catname1, catname2, catname3, catname4, ex
            FROM sphinx_products
            WHERE id IN (".implode(",", $ids).")
            ORDER BY FIELD(id, ".implode(",", $ids).")
        ");
        if($sth->rowCount() > 0) {
            while($result = $sth->fetch(PDO::FETCH_ASSOC)) {
                $products[$result['id']]['name'] = $result['name'];
                $products[$result['id']]['ex'] = $result['ex'];
                $products[$result['id']]['catname1'] = $result['catname1'];
                $products[$result['id']]['catname2'] = $result['catname2'];
                $products[$result['id']]['catname3'] = $result['catname3'];
                $products[$result['id']]['catname4'] = $result['catname4'];
            }
        }
        return $products;
    }

    function makeTrigrams($word) {
        $word = "__".$word."__";
        $len = mb_strlen($word);
        $result = array();
    
        for($i=0;$i<=$len-3;$i++) {
            $result[] = mb_substr($word, $i, 3);
        }
        return implode(" ", $result);
    }

    function generateKeywords() {
        // Генерируем список слов в каталоге
        $filename = "/tmp/keywords.txt";
        $cmd = "/usr/bin/indexer --buildstops ".$filename." 1000000 --buildfreqs products";
        exec($cmd);

        // Читаем строки
        $fp = fopen($filename, "r");
        if ($fp) {
            $this->_mysqlDbh->query("TRUNCATE TABLE sphinx_keywords");

            while (($buffer = fgets($fp, 4096)) !== false) {
                $arr = explode(" ", trim($buffer));
                if(!isset($arr[1]) || !is_numeric($arr[1])) {
                    continue;
                }
                if(preg_match('/[\d]+/', $arr[0])) {
                    continue;
                }
                $freq = $arr[1];
                $keyword = $arr[0];

                if(mb_strlen($keyword) > 1) {
                    $trigrams = $this->makeTrigrams($keyword);
                    $sql = "
                        INSERT INTO
                        sphinx_keywords (keyword,trigrams,freq)
                        VALUES (?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                        freq = freq + ?
                    ";
                    $sth = $this->_mysqlDbh->prepare($sql);
                    $sth->execute(array($keyword, $trigrams, $freq, $freq));
                    if($sth->errorCode() != "00000") {
                        echo "Чет какая-то ошибка.. [$keyword]\n";
                    }
                }
            }
            fclose($fp);
        }

        @unlink($filename);
    }

    private function _sphinxStopwords($query) {
        $stopwords = file('/etc/sphinx/stopwords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_walk($stopwords, 'trim');
        $words = explode(' ', $query);
        return implode(' ', array_diff($words, $stopwords));
    }

    private function _getSphinxQuery($query) {
        ### HOOKS ###
        $query = $this->_sphinxStopwords($query);
        #############

        $search = explode(" ", $this->_escapeString($query, false));
        $result = array();

        foreach($search as $word) {
            $word = trim(mb_strtolower($word));
            if ($word == "") continue;

            $words = array();

            if (mb_strlen($word) >= 2) {
                $words[] = $this->_findSynonym($word);
                $words[] = $this->_transliteString($word);
                $words[] = $this->_findMisspell(end($words));
                $words[] = $word;
                $words[] = $this->_findMisspell($word);
                $words[] = $this->_switchLayout($word);
                $words[] = $this->_identicalWordSwitcher($word);
            } elseif (mb_strlen($word) != 0) {
                $words[] = $word;
            }
            $words = array_unique($words);
            $words = array_map(function($value) {
                return $this->_escapeString($value);
            }, $words);

            //array_unshift($words, '"^'.$this->_escapeString($word).'$"');

            $result[] = '('.implode('|', $words).')';
        }

        $sphinx_query = '("'.$this->_escapeString($query, false).'") | (' . implode(' && ', $result) . ')';
        $this->sphinxQuery = $sphinx_query;

        return $sphinx_query;
    }

    private function _escapeString($string, $quotes=true) {
        $re = '/(?<size>[\d]+)([\s]+)(?<ed>gb)/mi';
        if (preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0) && count($matches) > 0) {
            $target = $matches[0][0];
            $replace = $matches[0]['size'].$matches[0]['ed'];
            $string = str_ireplace($target, $replace, $string);
        }

        /*$re = '/(?<l>mi)([\s]+)(?<n>[\d]+)/mi';
        if (preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0) && count($matches) > 0) {
            $target = $matches[0][0];
            $replace = $matches[0]['l'].$matches[0]['n'];
            $string = str_ireplace($target, $replace, $string);
        }*/

        $re = '/(?<n>[\d]+)(([\s]+|))(?<l>\+)/mi';
        if (preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0) && count($matches) > 0) {
            $target = $matches[0][0];
            $replace = $matches[0]['n'].' plus';
            $string = str_ireplace($target, $replace, $string);
        }

        $re = '/(?<l>\b[\w]{1,2})([\s]+)(?<n>[\d]{1,2}\b)/mi';
        if (preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0) && count($matches) > 0) {
            $target = $matches[0][0];
            $replace = $matches[0]['l'].$matches[0]['n'];
            $string = str_ireplace($target, $replace, $string);
        }

        $re = '/(?<l>\b[\d]{1,2})([\s]+)(?<n>[\w]{1,2}\b)/mi';
        if (preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0) && count($matches) > 0) {
            $target = $matches[0][0];
            $replace = $matches[0]['l'].$matches[0]['n'];
            $string = str_ireplace($target, $replace, $string);
        }


        $from = array ('\\', '(',')','|','!','@','~','"','&', '/', '^', '$', '=', ',', ';', '_', "\\");

        $result = str_replace ($from, ' ', $string);
        if ($quotes) $result = '"'.$result.'"';

        return $result;
    }

    private function _switchLayout($word){
        $str[0] = array(
            'й' => 'q', 'ц' => 'w', 'у' => 'e', 'к' => 'r', 'е' => 't', 'н' => 'y', 'г' => 'u', 'ш' => 'i', 'щ' => 'o',
            'з' => 'p', 'х' => '', 'ъ' => '', 'ф' => 'a', 'ы' => 's', 'в' => 'd', 'а' => 'f', 'п' => 'g', 'р' => 'h',
            'о' => 'j', 'л' => 'k', 'д' => 'l', 'ж' => '', 'э' => '', 'я' => 'z', 'ч' => 'x', 'с' => 'c', 'м' => 'v',
            'и' => 'b', 'т' => 'n', 'ь' => 'm', 'б' => '', 'ю' => '','Й' => 'Q', 'Ц' => 'W', 'У' => 'E', 'К' => 'R',
            'Е' => 'T', 'Н' => 'Y', 'Г' => 'U', 'Ш' => 'I', 'Щ' => 'O', 'З' => 'P', 'Х' => '', 'Ъ' => '', 'Ф' => 'A',
            'Ы' => 'S', 'В' => 'D', 'А' => 'F', 'П' => 'G', 'Р' => 'H', 'О' => 'J', 'Л' => 'K', 'Д' => 'L', 'Ж' => '',
            'Э' => '', '?' => 'Z', 'С' => 'C', 'М' => 'V', 'И' => 'B', 'Т' => 'N', 'Ь' => 'M', 'Б' => '', 'Ю' => ''
        );
        $str[1] = array(
            'q' => 'й', 'w' => 'ц', 'e' => 'у', 'r' => 'к', 't' => 'е', 'y' => 'н', 'u' => 'г', 'i' => 'ш', 'o' => 'щ',
            'p' => 'з', '[' => 'х', ']' => 'ъ', 'a' => 'ф', 's' => 'ы', 'd' => 'в', 'f' => 'а', 'g' => 'п', 'h' => 'р',
            'j' => 'о', 'k' => 'л', 'l' => 'д', ';' => 'ж', '\'' => 'э', 'z' => 'я', 'x' => 'ч', 'c' => 'с', 'v' => 'м',
            'b' => 'и', 'n' => 'т', 'm' => 'ь', ',' => 'б', '.' => 'ю','Q' => 'Й', 'W' => 'Ц', 'E' => 'У', 'R' => 'К',
            'T' => 'Е', 'Y' => 'Н', 'U' => 'Г', 'I' => 'Ш', 'O' => 'Щ', 'P' => 'З', 'A' => 'Ф', 'S' => 'Ы', 'D' => 'В',
            'F' => 'А', 'G' => 'П', 'H' => 'Р', 'J' => 'О', 'K' => 'Л', 'L' => 'Д', 'Z' => '?', 'X' => 'ч', 'C' => 'С',
            'V' => 'М', 'B' => 'И', 'N' => 'Т', 'M' => 'Ь');

        $res = strtr($word, array_merge($str[0], $str[1]));
        if ($res == $word) {
            $res = strtr($word, array_merge($str[1], $str[0]));
        }
        return strtolower($res);
    }

    private function _findSynonym($word) {
        $sql = "
            SELECT *
            FROM synonyms
            WHERE MATCH(:word)
            ORDER BY WEIGHT() DESC
            LIMIT 1
            OPTION ranker=bm25
        ";
        $sth = $this->_sphinxDbh->prepare($sql);
        $sth->bindParam(":word", $word, PDO::PARAM_STR);
        $sth->execute();

        if ($sth->rowCount() !== 1) return $word;

        $row = $sth->fetch();
        $word_id = $row['id'];

        $sth = $this->_mysqlDbh->prepare("SELECT keyword FROM sphinx_synonyms WHERE id = :id LIMIT 1");
        $sth->bindValue(':id', $word_id);
        $sth->execute();

        if ($sth->rowCount() === 1) return $sth->fetchColumn();

        return false;
    }

    private function _findMisspell($word) {
        $len = strlen($word);
        $trigrams = '"' . $this->makeTrigrams($word) . '"/3';

        $sql = "
          SELECT *, WEIGHT()+2-abs(len-:len)*0.8+ln(freq)/ln(1000) AS custom_rank
          FROM keywords
          WHERE MATCH(:trigrams) AND custom_rank > 0
          ORDER BY custom_rank DESC, WEIGHT() DESC, freq DESC
          LIMIT 1
          OPTION ranker=expr('sum(word_count-min_hit_pos)'), max_matches=1;";

        $sth = $this->_sphinxDbh->prepare($sql);

        $sth->bindParam(":len", $len, PDO::PARAM_INT);
        $sth->bindParam(':trigrams', $trigrams, PDO::PARAM_STR);
        $sth->execute();

        // Ничего не нашлось? Попробуем по другому.
        if ($sth->rowCount() !== 1) return $word;

        $word_id = $sth->fetchColumn();

        // Если слово нашлось, получаем его и возвращаем
        $sth = $this->_mysqlDbh->prepare("SELECT keyword FROM sphinx_keywords WHERE id = :id LIMIT 1");
        $sth->bindValue(':id', $word_id);
        $sth->execute();

        if ($sth->rowCount() === 1) return $sth->fetchColumn();

        return $word;
    }

    private function _transliteString($word){
        $tbl= array(
            'а'=>'a', 'А'=>'A', 'б'=>'b', 'Б'=>'B', 'в'=>'v', 'В'=>'V', 'г'=>'g', 'Г'=>'G', 'д'=>'d', 'Д'=>'D', 'е'=>'e',
            'Е'=>'E', 'ж'=>'zh', 'Ж'=>'ZH', 'з'=>'z', 'З'=>'Z', 'и'=>'i', 'И'=>'I', 'й'=>'y', 'Й'=>'Y', 'к'=>'k', 'К'=>'K',
            'л'=>'l', 'Л'=>'L', 'м'=>'m', 'М'=>'M', 'н'=>'n', 'Н'=>'N', 'о'=>'o', 'О'=>'O', 'п'=>'p', 'П'=>'P', 'р'=>'r',
            'Р'=>'R', 'с'=>'s', 'С'=>'S', 'т'=>'t', 'Т'=>'T', 'у'=>'u', 'У'=>'U', 'ф'=>'f', 'Ф'=>'F', 'ы'=>'y', 'Ы'=>'Y',
            'э'=>'e', 'Э'=>'E', 'ё'=>"yo", 'Ё'=>"YO", 'х'=>"h", 'Х'=>"H", 'ц'=>"ts", 'Ц'=>"TS", 'ч'=>"ch", 'Ч'=>"CH",
            'ш'=>"sh", 'Ш'=>"SH", 'щ'=>"sch", 'Щ'=>"SCH", 'ъ'=>"", 'Ъ'=>"", 'ь'=>"", 'Ь'=>"", 'ю'=>"yu", 'Ю'=>"YU",
            'я'=>"ya", 'Я'=>"YA"
        );
        $tbl2 = array(
            "a"=>"а","b"=>"б","v"=>"в","g"=>"г","d"=>"д","e"=>"е","yo"=>"ё", "j"=>"ж","z"=>"з","i"=>"и","k"=>"к", "l"=>"л",
            "m"=>"м","n"=>"н","o"=>"о","p"=>"п","r"=>"р","s"=>"с","t"=>"т", "y"=>"у","f"=>"ф","h"=>"х","c"=>"ц", "ch"=>"ч",
            "sh"=>"ш","u"=>"у","ya"=>"я","A"=>"А","B"=>"Б","V"=>"В","G"=>"Г","D"=>"Д", "E"=>"Е","Yo"=>"Ё","J"=>"Ж","Z"=>"З",
            "I"=>"И","K"=>"К","L"=>"Л","M"=>"М", "N"=>"Н","O"=>"О","P"=>"П", "R"=>"Р","S"=>"С","T"=>"Т","Y"=>"Ю","F"=>"Ф",
            "H"=>"Х","C"=>"Ц","Ch"=>"Ч","Sh"=>"Ш","U"=>"У","Ya"=>"Я","'"=>"ь","''"=>"ъ","ye"=>"є","YE"=>"Є" );
        $res = strtr($word, $tbl);
        if ($res == $word) {
            $res = strtr($word, $tbl2);
        }
        return strtolower($res);
    }

    private function _identicalWordSwitcher($word) {
        $word = mb_strtolower($word);
        $map = array('a' => 'а', 'e' => 'е', 't' => 'т', 'y' => 'у', 'u' => 'и', 'o' => 'о', 'p' => 'р', 'h' => 'н', 'k' => 'к', 'x' => 'х', 'c' => 'с', 'b' => 'в', 'm' => 'м');
        $new_word = strtr($word, $map);

        if ($new_word == $word) $new_word = strtr($word, array_flip($map));

        return $new_word;
    }
}