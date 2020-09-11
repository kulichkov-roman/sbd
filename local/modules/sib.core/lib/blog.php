<?
namespace Sib\Core;

/* use \Bitrix\Main\Loader;

\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale'); */

class Blog
{
    private $query = [];
    private $baseUrl = '';
    private $mainPage = '/blog/';
    private $currentPage = '';
    private $isMainPage = false;
    private $isLogoLink = false;
    private $topSort = [
        'top_day' => [
            'NAME' => 'За день'
        ],
        'top_week' => [
            'NAME' => 'За неделю'
        ],
        'top_month' => [
            'NAME' => 'За месяц'
        ],
        'top_year' => [
            'NAME' => 'За год'
        ],
        'top' => [
            'NAME' => 'За все время'
        ]
    ];
    private $tagList = [ 
        [ 
            'PICTURE_SRC' => '/img/menu/xiaomi.png',
            'NAME' => 'Xiaomi'
        ],
        [
            'PICTURE_SRC' => '/img/menu/huawei.png',
            'NAME' => 'Huawei'
        ],
        [
            'PICTURE_SRC' => '/img/menu/meizu.png',
            'NAME' => 'Meizu'
        ],
        [
            'PICTURE_SRC' => '/img/menu/oneplus.png',
            'NAME' => 'OnePlus'
        ]
    ];
    private $defTopSort = 'top_month';

    private $tag = '';
    private $order = '';

    private $arParams = [
        'IBLOCK_ID' => 1
    ];

    function __construct()
    {
        global $APPLICATION;

        $this->buildQueryArray();

        $this->mainPage = SITE_DIR . 'blog/';
        $this->currentPage = $APPLICATION->GetCurPage(false);
        $this->isMainPage = $this->currentPage === $this->mainPage;
        $this->isLogoLink = ($this->isMainPage && empty($_SERVER['QUERY_STRING']));

        $this->baseUrl = $this->getBaseUrl();

        $this->tag = $this->getQueryValue('tag');
        $this->order = $this->getQueryValue('order');
        $this->buildTopSortArray();
    }

       /*  public function getUserInfo()
        {
            global $USER;
            BlogRating::getUserRateList();
        } */

        private function buildQueryArray()
        {
            $query = explode('&', $_SERVER['QUERY_STRING']);
            $queryArray = [];
            foreach($query as $q){
                $t = explode('=', $q);
                $queryArray[htmlspecialchars($t[0])] = htmlspecialchars($t[1]);
            }

            $this->query = $queryArray;
        }

        private function buildTopSortArray()
        {
            foreach($this->topSort as $order => $value){
                $this->topSort[$order]['LINK'] = !empty($this->tag) ? "{$this->baseUrl}?order={$order}&tag={$this->tag}" : "{$this->baseUrl}?order={$order}";
            }
        }

    public function getBaseUrl()
    {
        return (!$this->isMainPage && strpos($this->currentPage, '.html') === false) ? $this->currentPage : $this->mainPage;
    }

    public function getQueryValue($value = false)
    {
        return !empty($value) && is_array($this->query) && isset($this->query[$value]) && strlen($this->query[$value]) ? $this->query[$value] : false;
    }

    public function getMainPageUrl()
    {
        return $this->mainPage;
    }

    public function isLogoLink()
    {
        return $this->isLogoLink;
    }

    public function isActiveTopOrder()
    {
        return !empty($this->query['order']) && $this->query['order'] !== 'new';
    }

    public function isActiveNewOrder()
    {
        return empty($this->query['order']) || $this->query['order'] === 'new';
    }

    public function getDefaultTopSort()
    {
        return $this->defTopSort;
    }

    public function getTopSortArray()
    {
        return $this->topSort;
    }

    public function getNewSortLink()
    {
        return !empty($this->tag) ? "{$this->baseUrl}?order=new&tag={$this->tag}" : "{$this->baseUrl}?order=new";
    }

    public function getOrder()
    {
        $defaultOrder = [
            'field' => 'SHOWS',
            'by' => 'DESC',
            'field2' => 'ID',
            'by2' => 'DESC'
        ];

        $isTop = false;
        if(strpos($this->order, 'top') === 0){
            $isTop = true;
        }

        if(empty($this->order) || $this->order === 'new'){
            $defaultOrder['field'] = 'DATE_ACTIVE_FROM';
            $defaultOrder['by'] = 'DESC';
        } else if($isTop){
            $defaultOrder['field'] = 'PROPERTY_BLOG_RATING';
            $defaultOrder['by'] = 'DESC';
        }
       
        return $defaultOrder;
    }

    public function getFilter()
    {
        $blogFilter = [];
        if($this->tag){
            $blogFilter['%TAGS'] = htmlspecialchars(trim(strtolower(urldecode($this->tag))));
        }

        if(strpos($this->order, 'top') === 0){
            $date = new \DateTime();
            switch($this->order){
                case 'top_day':
                    $blogFilter['>=DATE_ACTIVE_FROM'] = $date->modify('-1 day')->format('d.m.Y H:i:s');
                break;
                case 'top_week':
                    $blogFilter['>=DATE_ACTIVE_FROM'] = $date->modify('-7 day')->format('d.m.Y H:i:s');
                break;
                case 'top_month':
                    $blogFilter['>=DATE_ACTIVE_FROM'] = $date->modify('-1 month')->format('d.m.Y H:i:s');
                break;
                case 'top_year':
                    $blogFilter['>=DATE_ACTIVE_FROM'] = $date->modify('-1 year')->format('d.m.Y H:i:s');
                break;
            }
        }

        $blogFilter['ACTIVE_DATE'] = 'Y';

        return $blogFilter;
    }

    public function getAsideCommentTemplate()
    {
        return $this->getCache('buildCommentHtml', ['time' => 3600, 'cacheId' => ['getAsideCommentTemplate']]); 
    }

        private function buildCommentHtml()
        {
            if($comment = BlogComments::getTopComment()){
                ob_start();
                $item = \CIblockElement::GetList([], ['IBLOCK_ID' => 1, 'ID' => $comment['UF_ITEM_ID'], false, false, ['ID', 'NAME', 'DETAIL_PAGE_URL']])->getNext();
                $user = current($comment['USER']);
                $user['NAME'] = $user['NAME'] ? $user['NAME'] : $user['LOGIN'];
            ?> 
                <div class="aside__block">
                    <div class="aside__block_head">Комментарий дня</div>
                    <div class="aside__block_body">
                        <div class="top-comment neitral js-like-check" data-entity="comment" data-id="<?=$comment['ID']?>">
                            <div class="top-comment__likes likes__count">
                                <?=$comment['UF_LIKES_COUNT']?>
                            </div>
                            <div class="top-comment__author">
                                <div class="top-comment__author_photo">
                                    <img src="<?=$user['PERSONAL_PHOTO']?:'/local/templates/sibdroid_blog/img/svg/avatar.svg'?>" alt="<?=$$user['NAME']?>">
                                </div>
                                <div class="top-comment__author_inf">
                                    <div class="top-comment__author_name"><?=$user['NAME']?></div>
                                    <div class="top-comment__author_time"><?=$comment['DATE_TEXT']?></div>
                                </div>
                            </div>
                            <div class="top-comment__text">
                                <?=$comment['UF_TEXT']?>
                            </div>
                            <div class="top-comment__link">
                                <a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?

                return ob_get_clean();
            }

            return '';
        }

    
    private $itemsBlockListParams = [];
    
    public function getAsideItemsBlockTemplate($params = [])
    {
        $this->itemsBlockListParams = $params;
        $list = $this->getCache('buildAsideItemsBlockList', ['time' => 3600, 'cacheId' => $params]);
        if(count($list) <= 0) return '';
            ob_start();
        ?>
            <div class="aside__block">
                    <div class="aside__block_head"><?=$params['TITLE']?></div>
                    <div class="aside__block_body">
                        <div class="top-news">
                            <?foreach($list as $item):?>
                            <?
                                $defPic = SITE_TEMPLATE_PATH . '/img/svg/avatar.svg';
                            ?>
                                <div class="top-news__item">
                                    <div class="top-news__item_pic">
                                        <img src="<?=$item['PIC']?:$defPic?>" alt="<?=$item['NAME']?>">
                                    </div>
                                    <div class="top-news__item_inf">
                                        <div class="top-news__item_name">
                                            <a href="<?=$item['URL']?>"><?=$item['NAME']?></a>
                                        </div>
                                        <div class="top-news__item_comments">
                                            <div class="grid">
                                                <svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_comments"></use></svg>
                                                <span><?=$item['COMMENTS']?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?endforeach?>
                        </div>
                    </div>
                </div>
        <?
            return ob_get_clean();
    }

        private function buildAsideItemsBlockList()
        {
            $list = [];
            if(is_array($this->itemsBlockListParams) && isset($this->itemsBlockListParams['ORDER']) && isset($this->itemsBlockListParams['FILTER'])){
                $rs = \CIblockElement::GetList($this->itemsBlockListParams['ORDER'], $this->itemsBlockListParams['FILTER'], false, false, ['ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'PROPERTY_BLOG_COMMENTS']);
                while($ob = $rs->GetNext()){

                    $pic = false;
                    if($ob['PREVIEW_PICTURE'] > 0){
                        $arPhotoSmall = \CFile::ResizeImageGet(
                            $ob['PREVIEW_PICTURE'], 
                            [
                                'width'=>40,
                                'height'=>40
                            ], 
                            BX_RESIZE_IMAGE_EXACT,
                            [
                                "name" => "sharpen", 
                                "precision" => 0
                            ]
                        );
                        $pic = $arPhotoSmall['src'];
                    }

                    $list[] = [
                        'ID' => $ob['ID'],
                        'NAME' => $ob['NAME'],
                        'PIC' => $pic,
                        'URL' => $ob['DETAIL_PAGE_URL'],
                        'COMMENTS' => (int)$ob['PROPERTY_BLOG_COMMENTS_VALUE']
                    ];
                }
            }

            return $list;
            
        }

    public function getAsideTagListTemplate()
    {
        return $this->getCache('buildTagListHtml', ['cacheId' => [$this->tag ?:'emptyTag', $this->order]]);    
    }

        private function buildTagListHtml()
        {            
            $tmpl = '';
            foreach($this->tagList as $tag){
                $selected = $this->tag === $tag['NAME'] ? 'selected' : '';
                $href = !empty($this->order) ? "{$this->baseUrl}?order={$this->order}&tag={$tag['NAME']}" : "{$this->baseUrl}?tag={$tag['NAME']}";
                $pic = SITE_TEMPLATE_PATH . $tag['PICTURE_SRC'];
                $tmpl .= "<a class='list-aside__item {$selected}' href='{$href}'> <img src='{$pic}' alt='{$tag['NAME']}'>{$tag['NAME']}</a>";
            }
            return $tmpl;
        }

    public function getAsideSectionsListTemplate()
    {    
        $this->sectionListArray = $this->getCache('getSectionListArray', ['cacheId' => 'sectionListCache']);
        return $this->getCache('buildSectionListHtml', ['cacheId' => [$this->baseUrl, $this->order]]);        
    }

        private function buildSectionListHtml()
        {            
            $tmpl = '';
            foreach($this->sectionListArray as $item){
                $selected = $item['SECTION_PAGE_URL'] === $this->currentPage ? 'selected' : '';
                $href = $item['SECTION_PAGE_URL'];
                if($this->order) $href .= '?order=' . $this->order;
                $tmpl .= "<a class='list-aside__item {$selected}' href='{$href}'><img src='{$item['PICTURE_SRC']}' alt='{$item['NAME']}'>{$item['NAME']}</a>";
            }    
            return $tmpl;
        }

        private function getSectionListArray()
        {
            $sectionList = [];
            $rs = \CIblockSection::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'], false, ['ID', 'NAME', 'CODE', 'PICTURE', 'SECTION_PAGE_URL']);
            while($ob = $rs->GetNext()){
                if($ob['PICTURE']){
                    $ob['PICTURE_SRC'] = \CFIle::GetPath($ob['PICTURE']);
                }
                $sectionList[$ob['ID']] = $ob;
            }
            return $sectionList;
        }

    private function getCache($method, $cacheParams = false)
    {
        $cacheParamsLocal = [
            'time' => 86400 * 365,
            'cacheId' => 'defaultCache',
            'cacheDir' => '/'.SITE_ID.'/sib_blog/'
        ];

        foreach($cacheParams as $key => $param){
            if(isset($cacheParamsLocal[$key])){
                $cacheParamsLocal[$key] = $param;
            }
        }

        $obCache = new \CPHPCache();
        $result = false;
        if($obCache->InitCache($cacheParamsLocal['time'], md5(serialize($cacheParamsLocal['cacheId'])), $cacheParamsLocal['cacheDir'] . $cacheParamsLocal['cacheId'])){
            $vars = $obCache->GetVars();
            $result = $vars['result'];
        } else {
            $result = call_user_func(array($this, $method));
            if($obCache->StartDataCache()){
                $obCache->EndDataCache(array('result' => $result));
            }  
        }

        return $result;
    }

    public static function agentStatisticItems()
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById(6)->fetch(); 
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass(); 

        
        $date = new \DateTime();
        $currentDate = $date->format('d.m.Y');
        $currentDateTime = $date->format('d.m.Y H:i:s');
        $startDate = $date->modify('-30 days')->format('d.m.Y');
        
        $rsItems = \CIblockElement::GetList(['DATE_CREATE' => 'ASC', 'ID' => 'ASC'], ['IBLOCK_ID' => 1, '>=TIMESTAMP_X' => $startDate , '<=TIMESTAMP_X' => $currentDate], false, false, ['ID', 'SHOW_COUNTER']);
        while($ob = $rsItems->GetNext()){
            $entityDataClass::add([
                'UF_ITEM_ID' => $ob['ID'],
                'UF_DATE' => $currentDateTime,
                'UF_VIEWS' => $ob['SHOW_COUNTER'],
                'UF_COMMENTS' => 0
            ]);
        }
    }

    public static function getRatingInfo($ids = [])
    {
        if (\Bitrix\Main\Loader::includeModule('iblock') && !empty($ids)) {
            $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 1, '=ID' => $ids], false, false, ['ID', 'PROPERTY_BLOG_COMMENTS', 'PROPERTY_BLOG_LIKES']);
            $resultItems = [];
            while($ob = $rs->GetNext()){
                $resultItems[$ob['ID']] = [
                    'LIKES' => (int)$ob['PROPERTY_BLOG_LIKES_VALUE'],
                    'COMMENTS' => (int)$ob['PROPERTY_BLOG_COMMENTS_VALUE']
                ];
            }
            return $resultItems;
        }

        return [];
    }

    public static function getShowCounterInfo($ids = [])
    {
        if (\Bitrix\Main\Loader::includeModule('iblock') && !empty($ids)) {
            $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 1, '=ID' => $ids], false, false, ['ID', 'SHOW_COUNTER']);
            $resultItems = [];
            while($ob = $rs->GetNext()){
                $resultItems[$ob['ID']] = [
                    'SHOW_COUNTER' => (int)$ob['SHOW_COUNTER']
                ];
            }
            return $resultItems;
        }

        return [];
    }

    public static function updateItemRating($id = 0, $cnt = 0)
    {
        if(\Bitrix\Main\Loader::includeModule('iblock') && $id > 0){
            $resultSum = 0;

            if($cnt > 0){
                $resultSum = $cnt * 3;
            } else if ($cnt < 0){
                $resultSum = abs($cnt) * 2;
            }

            $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 1, 'ID' => $id], false, false, ['ID', 'PROPERTY_BLOG_COMMENTS']);
            if($ob = $rs->GetNext()){
                $commentCount = (int)$ob['PROPERTY_BLOG_COMMENTS_VALUE'];
                $resultSum += $commentCount;
            }

            \CIBlockElement::SetPropertyValuesEx($id, 1, ['BLOG_RATING' => (int)$resultSum, 'BLOG_LIKES' => (int)$cnt]);
        }
    }

    public static function updateItemComment($id = 0, $comment = 0)
    {
        if(\Bitrix\Main\Loader::includeModule('iblock') && $id > 0){
            \CIBlockElement::SetPropertyValuesEx($id, 1, ['BLOG_COMMENTS' => (int)$comment]);
        }
    }
}