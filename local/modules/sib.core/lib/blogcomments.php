<?
namespace Sib\Core;

use Bitrix\Main\Loader; 
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

class BlogComments
{
    private static $entityCommentId = 8;
    private static $elementPerPage = 10;

    public static function addComment($elementId = 0, $commentParentId = 0, $text = [])
    {
        if(!isset($_SESSION['BLOG_COMM_COUNT'])){
            $_SESSION['BLOG_COMM_COUNT'] = 0;
        } else {
            if($_SESSION['BLOG_COMM_COUNT'] > 20){
                return ['TYPE' => 'ERROR_ACTIVE_SESSION', 'MSG' => 'Многа жмем', 'NEED_DISABLED' => true];
            }
            $_SESSION['BLOG_COMM_COUNT'] += 1;
        }

        global $USER;
        $uid = (int)$USER->getID();

        if($uid > 0 && !empty($text) && count($text) > 0){
            $commentDataClass = self::getEntityDataClass(self::$entityCommentId);
            $arCommentAddFields = [
                'UF_ITEM_ID' => $elementId,
                'UF_COMMENT_ID' => $commentParentId,
                'UF_DATE' => date('d.m.Y H:i:s'),
                'UF_USER' => $uid,
                'UF_TEXT' => serialize($text),
                'UF_ACTIVE' => 1,
                'UF_RATING' => 0,
                'UF_DEPTH_LEVEL' => 1,
                'UF_LIKES_COUNT' => 0
            ];
            $result = $commentDataClass::add($arCommentAddFields);

            $commentId = $result->getId();
            
            if($commentParentId > 0){
                $rsData = $commentDataClass::getList(['filter' => ['ID' => $commentParentId]])->fetch();
                $arUpd = [
                    'UF_DEPTH_LEVEL' => $rsData['UF_DEPTH_LEVEL'] + 1,
                    'UF_PATH' => (!empty($rsData['UF_PATH']) ? $rsData['UF_PATH'] : $rsData['ID']) . '.' . $commentId
                ];

                $commentDataClass::update($commentId, $arUpd);
            }

            if($commentId > 0){
                $count = $commentDataClass::getList([
                    'select' => [new Entity\ExpressionField('CNT', 'COUNT(1)')],
                    'filter' => [
                        'UF_ITEM_ID' => $elementId,
                        'UF_ACTIVE' => 1
                    ]
                ])->fetch()['CNT'];
                Blog::updateItemComment($elementId, $count);

                $arCommentAddFields['ID'] = $commentId;
                $arCommentAddFields['CHILD'] = [
                    'META' => [
                        'SIZE_ALL_LEVEL' => 0
                    ]
                ];
                $arCommentAddFields['UF_TEXT'] = implode("<br>", unserialize($arCommentAddFields['UF_TEXT']));

                return [
                    'ID' => $commentId,
                    'META' => [
                        'SIZE_ALL_LEVEL' => 1
                    ],
                    'ROWS' => [
                        $commentId => $arCommentAddFields
                    ],
                    'USERS' => self::getUserList([$uid => 1]),
                ];
            }

            
        }

        return ['ID' => 0];
    }

    public static function getTopComment()
    {
        $commentDataClass = self::getEntityDataClass(self::$entityCommentId);
        $date = new \DateTime();
        $comment = $commentDataClass::getList([
            'order' => ['UF_RATING' => 'DESC', 'ID' => 'DESC'],
            'filter' => [
                'UF_ACTIVE' => 1,
                '>=UF_DATE' => \Bitrix\Main\Type\DateTime::createFromUserTime($date->modify('-1 day')->format('d.m.Y') . ' 00:00:00'),
                '<=UF_DATE' => \Bitrix\Main\Type\DateTime::createFromUserTime($date->modify('+1 day')->format('d.m.Y') . ' 23:59:59')
            ],
            'limit' => 1
        ])->fetch();

        if($comment['ID'] > 0){
            $comment['USER'] = self::getUserList([$comment['UF_USER'] => 1]);
            $comment['RATING_CLASS'] = BlogRating::getRatingClass($comment['UF_LIKES_COUNT']);
            $comment['DATE_TEXT'] = self::getTimeStr($comment['UF_DATE']); 
            $comment['UF_TEXT'] = unserialize($comment['UF_TEXT']) ? implode("<br>", unserialize($comment['UF_TEXT'])) : $comment['UF_TEXT'];
        }
        

        return  $comment['ID'] > 0 ? $comment : false;
    }

    public static function editComment($commentId = 0, $text = [])
    {
        global $USER;
        $uid = (int)$USER->getID();

        if($uid > 0 && !empty($text) && count($text) > 0){
            $commentDataClass = self::getEntityDataClass(self::$entityCommentId);
            $comment = $commentDataClass::getList(['filter' => ['=ID' => $commentId]])->fetch();
            if((int)$comment['UF_USER'] === $uid){
                $result = $commentDataClass::update($commentId, ['UF_TEXT' => serialize($text)]);
                if($cid = $result->getId()){ 
                    return ['ID' => $cid, 'TEXT' => implode("<br>", $text)]; 
                }
            }
        }

        return ['ID' => 0];
    }

    public static function updateRating($id = 0, $cnt = 0)
    {
        $resultSum = 0;
        if($id > 0){
            $commentDataClass = self::getEntityDataClass(self::$entityCommentId);

            if($cnt > 0){
                $resultSum = $cnt * 3;
            } else if ($cnt < 0){
                $resultSum = abs($cnt) * 2;
            }

            $comment = $commentDataClass::getList(['filter' => ['ID' => $id]])->fetch();
            if((int)$comment['UF_COMMENT_ID'] === 0){
                $countChild = $commentDataClass::getList([
                    'select' => [new Entity\ExpressionField('CNT', 'COUNT(1)')],
                    'filter' => [
                        'UF_ITEM_ID' => $comment['UF_ITEM_ID'],
                        'UF_ACTIVE' => 1,
                        'UF_PATH' => $comment['ID'] . '.%'
                    ]
                ])->fetch()['CNT'];

                if($countChild > 0){
                    $resultSum += $countChild;
                }
            }            

            $commentDataClass::update($id, [
                'UF_RATING' => $resultSum,
                'UF_LIKES_COUNT' => $cnt
            ]);
        }        

        return $resultSum;
    }

    public static function getComments($elementId = 0, $commentParentId = 0, $pageNum = 1, $sort = 'RATING')
    {
        $result = [];

        $commentDataClass = self::getEntityDataClass(self::$entityCommentId);

        $order = ['UF_RATING' => 'DESC', 'ID' => 'DESC'];
        if($sort === 'NEW'){
            $order = ['ID' => 'DESC'];
            if($commentParentId > 0){
                $order = ['ID' => 'ASC'];
            }
        }

        $filter = [
            'UF_ITEM_ID' => $elementId,
            'UF_ACTIVE' => 1
        ];

        if($commentParentId === 0){
            $result['META']['SIZE_ALL'] = $commentDataClass::getList([
                'select' => [new Entity\ExpressionField('CNT', 'COUNT(1)')],
                'filter' => $filter
            ])->fetch()['CNT'];
            $result['META']['SIZE_ALL_TEXT'] = self::getSizeAllText($result['META']['SIZE_ALL']);
        }

        $result['META']['PARENT_COMMENT_ID'] = $commentParentId;
        
        $filter['UF_COMMENT_ID'] = $commentParentId;

        $result['META']['SIZE_ALL_LEVEL'] = $commentDataClass::getList([
            'select' => [new Entity\ExpressionField('CNT', 'COUNT(1)')],
            'filter' => $filter
        ])->fetch()['CNT'];


        $result['META']['PAGE_COUNT_LEVEL'] = ceil($result['META']['SIZE_ALL_LEVEL'] / self::$elementPerPage);    

        if($pageNum > $result['META']['PAGE_COUNT_LEVEL']){
            return $result;
        } else if($pageNum < $result['META']['PAGE_COUNT_LEVEL']) {
            $result['META']['NEXT_PAGE_NUM'] = $pageNum + 1;
        }
        
        $rsData = $commentDataClass::getList([
            'order' => $order,
            'filter' => $filter,
            'limit' => self::$elementPerPage,
            'offset' => ($pageNum - 1) * self::$elementPerPage
        ]);
        
        while($arData = $rsData->fetch()){
            $result['USERS'][$arData['UF_USER']] = true;      

            $arData['RATING_CLASS'] = 'neitral'; //BlogRating::getRatingClass($arData['UF_LIKES_COUNT']);
            $arData['DATE_TEXT'] = self::getTimeStr($arData['UF_DATE']); 
            $arData['CHILD'] = self::getComments($elementId, $arData['ID'], 1, 'NEW');
            $arData['UF_TEXT'] = unserialize($arData['UF_TEXT']) ? implode("<br>", unserialize($arData['UF_TEXT'])) : $arData['UF_TEXT'];
            $result['ROWS'][] = $arData;
        }

        $result['USERS'] = self::getUserList($result['USERS']);

        return $result;
    }

    public static function getEntityDataClass($entityId = false)
    {
        if(!Loader::includeModule("highloadblock")) return false;

        $hlblock = HL\HighloadBlockTable::getById($entityId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
        return $entity->getDataClass();
    }

    public static function getSizeAllText($sizeAll = 0)
    {        
        $text = Helper::getPlural($sizeAll, 'комментарий', 'комментария', 'комментариев');
        return "{$sizeAll} {$text}";
    }

    public function getTimeStr($dateTime)
    {
        $tdStamp = strtotime('today');
        $ysStamp = strtotime('yesterday');

        $ts = $dateTime->getTimestamp();
        if($ts >= $tdStamp){
            $dateStr = 'Сегодня';
        } else if($ts >= $ysStamp){
            $dateStr = 'Вчера';
        } else {
            $dateStr = $dateTime->format('d.m.Y');
        }

        return $dateStr . ' в ' . $dateTime->format('H:i');
    }

    public function getUserList($userList = [])
    {
        $result = [];

        if(is_array($userList) && count($userList) > 0){
            $userList = array_keys($userList);
            $res = \Bitrix\Main\UserTable::getList(Array(
                "select" => ['ID', 'NAME', 'LAST_NAME', 'LOGIN', 'PERSONAL_PHOTO'],
                "filter"=> ['ID' => $userList],
             ));
            while($ob = $res->fetch()){
                
                if($ob['PERSONAL_PHOTO']){
                    $ob['PERSONAL_PHOTO'] = \CFile::GetPath($ob['PERSONAL_PHOTO']);
                    if(strpos($ob['PERSONAL_PHOTO'], 'avatar.png') !== false){
                        unset($ob['PERSONAL_PHOTO']);
                    }
                }

                if($ob['NAME'] && $ob['LAST_NAME']){
                    $ob['NAME'] .= ' ' . $ob['LAST_NAME'];
                }

                $result[$ob['ID']] = $ob;
            }
        }

        return $result;
    }
}
