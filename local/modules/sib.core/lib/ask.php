<?
namespace Sib\Core;

use \Bitrix\Main\Loader;
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('iblock');

class Ask
{
    private static $askIblockId = 51;
    private static $propTypeCode = 'TYPE';
    private static $arCheckProps = [
        'ITEM_ID', 'TYPE', 'ASK_ID', 'EMAIL', 'NAME', 'USER_ID', 'LIKE', 'DIS_LIKE', 'IP', 'RATING'
    ];

    public static function isAvailableFuture()
    {
        return self::$askIblockId > 0 && self::getPropIds() && self::getTypes();
    }

    public static function getItemIdByAskId($askId)
    {
        $types  = self::getTypes();
        $rsAsk = \CIblockElement::GetList([], ["ACTIVE"=>"Y", "IBLOCK_ID"=> self::$askIblockId, '=ID' => $askId], false, false, ['ID', 'PROPERTY_ITEM_ID']);
        if($ob = $rsAsk->GetNext()){
            return $ob['PROPERTY_ITEM_ID_VALUE'];
        }
        return false;
    }

    public static function getComponent($itemId = 0)
    {
        global $arFilterAskAns, $APPLICATION;
        if(Helper::isSmarPhoneItem($itemId)){
            $otherItems = Helper::getOtherItemFromSection($itemId);
            $arFilterAskAns = ['=PROPERTY_ITEM_ID' => $otherItems, '=PROPERTY_' . self::$propTypeCode => self::getTypeAskId()];
        } else {
            $arFilterAskAns = ['=PROPERTY_ITEM_ID' => $itemId, '=PROPERTY_' . self::$propTypeCode => self::getTypeAskId()];
        }
        
        $APPLICATION->includeComponent('bitrix:news.list', 'tab_ask', array(
            'AJAX_DIR' => '/ajax/sib/ask.php', 
            'IBLOCK_ID' => self::$askIblockId,
            'FILTER_NAME' => 'arFilterAskAns',
            'ITEM_ID' => $itemId,
            'USE_FILTER' => 'Y',
            'SORT_BY1' => 'ID',
            'SORT_ORDER1' => 'DESC',
            'NEWS_COUNT' => 5,
            'PROPERTY_CODE' => array(
                'ITEM_ID',
                'TYPE',
                'ASK_ID',
                'EMAIL',
                'NAME',
                'USER_ID'
            ),
            'FIELD_CODE' => array(
                'DATE_CREATE',
                'DATE'
            ),
            'ACTIVE_DATE_FORMAT' => 'j F Y',
            'DISPLAY_DATE' => 'Y',
            'IBLOCK_PROPS_IDS' => self::getPropIds(),
            'PROP_TYPE_IDS' => self::getTypes(),
            'PAGER_TEMPLATE' => 'sib_default',
            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
            'ADD_SECTIONS_CHAIN' => 'N'
        ), false);
    }
    
    public static function getPropIds()
    {
        $dbProps = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=> self::$askIblockId));
        $result = [];
        while($obProp = $dbProps->GetNext()){
            if(in_array($obProp['CODE'], self::$arCheckProps)){
                $result[$obProp['CODE']] = $obProp['ID'];
            } 
        }

        if(count(self::$arCheckProps) === count($result)){
            return $result;
        }

        return false;
    }

    public static function getTypes()
    {
        $dbEnum = \CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=> self::$askIblockId, "CODE" => self::$propTypeCode));
        $result = [];
        while($obEnum = $dbEnum->GetNext()){
            $result[$obEnum['XML_ID']] = $obEnum['ID'];
        }
        return $result['ASK'] > 0 && $result['ANS'] > 0 ? $result : false;
    }
        public static function getTypeAskId()
        {
            $types = self::getTypes();
            return  $types ? $types['ASK'] : false;
        }

        public static function getTypeAnsId()
        {
            $types = self::getTypes();
            return  $types ? $types['ANS'] : false;
        }

    public static function getCountAsk($itemId)
    {
        $types  = self::getTypes();
        if(Helper::isSmarPhoneItem($itemId)){
            $otherItems = Helper::getOtherItemFromSection($itemId);
            $arFilterAskAns = ["ACTIVE"=>"Y", "IBLOCK_ID"=> self::$askIblockId, '=PROPERTY_ITEM_ID' => $otherItems, '=PROPERTY_' . self::$propTypeCode => self::getTypeAskId()];
        } else {
            $arFilterAskAns = ["ACTIVE"=>"Y", "IBLOCK_ID"=> self::$askIblockId, '=PROPERTY_ITEM_ID' => $itemId, '=PROPERTY_' . self::$propTypeCode => self::getTypeAskId()];
        }

        $rsAsk = \CIblockElement::GetList([], $arFilterAskAns);
        return $rsAsk->SelectedRowsCount();
    }

    public static function getAskMenuArr(&$aMenu)
    {
        $aMenu = array(
            "parent_menu" => "global_menu_services",
            "sort"        => 0,
            "url"         => "sib_ask.php?IBLOCK_ID=".self::$askIblockId."&type=references&lang=".LANGUAGE_ID,
            "text"        => 'Вопрос-ответ',
            "title"       => 'Вопрос-ответ',
            "icon"        => "iblock_menu_icon_types",
            "page_icon"   => "iblock_menu_icon_types",
            "items_id"    => "menu_webforms",
            "items"       => [
                [
                    "text" => "Вопросы",
                    "url"  => "sib_ask.php?IBLOCK_ID=".self::$askIblockId."&TYPE=".self::getTypeAskId()."&ACTIVE=N&type=references&lang=".LANGUAGE_ID,
                    "icon" => "form_menu_icon",
                    "page_icon" => "form_page_icon",
                    "more_url" => [
                        "sib_ask.php?IBLOCK_ID=".self::$askIblockId."&TYPE=".self::getTypeAskId()."&ACTIVE=Y&type=references&lang=".LANGUAGE_ID,
                    ]
                ],
                [
                    "text" => "Ответы",
                    "url"  => "sib_ask.php?IBLOCK_ID=".self::$askIblockId."&TYPE=".self::getTypeAnsId()."&ACTIVE=N&type=references&lang=".LANGUAGE_ID,
                    "icon" => "forum_menu_icon",
                    "page_icon" => "forum_menu_icon",
                    "more_url" => [
                        "sib_ask.php?IBLOCK_ID=".self::$askIblockId."&TYPE=".self::getTypeAnsId()."&ACTIVE=Y&type=references&lang=".LANGUAGE_ID,
                    ]
                ]
            ]
        );
    }

    public static function addAnsUserAjax()
    {
        $_POST['ans'] = trim($_POST['ans']);
        if(empty($_POST['ans'])){
            echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => 'Введите текст ответа!']);
            return;
        }
        if((int)$_POST['askId'] <= 0){
            echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => 'Нужен вопрос, чтобы на него ответить :(']);
            return;
        }

        $propIds = self::getPropIds();
        $el = new \CIBlockElement;
        $arLoadProductArray = [
            'IBLOCK_ID' => self::$askIblockId,
            'ACTIVE' => 'Y',
            'PREVIEW_TEXT' => $_POST['ans'],
            'PROPERTY_VALUES' => [
                $propIds['ITEM_ID'] => self::getItemIdByAskId($_POST['askId']),
                $propIds['TYPE'] => self::getTypeAnsId(),
                $propIds['NAME'] => $_POST['userName'],
                $propIds['ASK_ID'] => $_POST['askId'],
                $propIds['USER_ID'] => $_POST['userId']
            ],
            'NAME' => 'Ответ на вопрос (сотрудник): ' . $_POST['askId'] . ' (' . $_POST['name'] . ' [' . $_POST['email'] . '])'
        ];

        if($el->Add($arLoadProductArray) && $el->Update($_POST['askId'], ['ACTIVE' => 'Y'])){
            echo json_encode((object)['TYPE' => 'OK', 'MSG' => 'Ответ и вопрос опубликованы!']);
            return;
        } else {
            echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => 'Произошла ошибка :( Пожалуйста, сообщите текст ошибки руководству: ' . $el->LAST_ERROR]);
            return;
        }
    }
}