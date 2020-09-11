<?
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

use Bitrix\Main\Loader;
include_once "include_stop_statistic.php";
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sib.core');

if($_POST['type'] == 'addAnsUser'){
    return \Sib\Core\Ask::addAnsUserAjax();
}

if(is_array($_POST['params']) && is_array($_POST['filter']) && \Bitrix\Main\Loader::includeModule('iblock')){
    include_once $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php";

    $arParams = $_POST['params'];
    
    if($_POST['type'] == 'ask'){
        /* $rs = CIblockElement::GetList([], ['IBLOCK_ID' => $_POST['params']['IBLOCK_ID'], 'PROPERTY_ITEM_ID' => $_POST['params']['ITEM_ID'], 'PROPERTY_IP' => $_SERVER['REMOTE_ADDR']]);
        if($rs->GetNext()){
            echo json_encode((object)['TYPE' => 'OK', 'MSG' => 'Вы уже оставляли вопрос к этому товару.']);
            die();
        } else { */
            $el = new CIBlockElement;
            $arLoadProductArray = [
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'ACTIVE' => 'N',
                'PREVIEW_TEXT' => $_POST['ask'],
                'PROPERTY_VALUES' => [
                    $arParams['IBLOCK_PROPS_IDS']['ITEM_ID'] => $arParams['ITEM_ID'],
                    $arParams['IBLOCK_PROPS_IDS']['TYPE'] => $arParams['PROP_TYPE_IDS']['ASK'],
                    $arParams['IBLOCK_PROPS_IDS']['EMAIL'] => $_POST['email'],
                    $arParams['IBLOCK_PROPS_IDS']['NAME'] => $_POST['name'],
                    $arParams['IBLOCK_PROPS_IDS']['IP'] => $_SERVER['REMOTE_ADDR']
                ],
                'NAME' => 'Вопрос к товару: ' . $arParams['ITEM_ID'] . ' (' . $_POST['name'] . ' [' . $_POST['email'] . '])'
            ];
            if($el->Add($arLoadProductArray)){
                echo json_encode((object)['TYPE' => 'OK', 'MSG' => 'Спасибо за вопрос! После модерации мы опубликуем вопрос на сайте.']);
                die();
            } else {
                echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => 'Произошла ошибка :( Пожалуйста, сообщите текст ошибки консультанту в чат: ' . $el->LAST_ERROR]);
                die();
            }
        //}
    }

    if ($_POST['type'] == 'ans') {
        //$rs = CIblockElement::GetList([], ['IBLOCK_ID' => $_POST['params']['IBLOCK_ID'], 'PROPERTY_ASK_ID' => $_POST['askId'], 'PROPERTY_ITEM_ID' => $_POST['params']['ITEM_ID'], 'PROPERTY_IP' => $_SERVER['REMOTE_ADDR']]);
        /* if($rs->GetNext()){
            echo json_encode((object)['TYPE' => 'OK', 'MSG' => 'Вы уже оставляли ответ к этому вопросу.']);
            die();
        } else { */
            $el = new CIBlockElement;
            $arLoadProductArray = [
                'IBLOCK_ID' => $_POST['params']['IBLOCK_ID'],
                'ACTIVE' => 'N',
                'PREVIEW_TEXT' => $_POST['ans'],
                'PROPERTY_VALUES' => [
                    $arParams['IBLOCK_PROPS_IDS']['ITEM_ID'] => $arParams['ITEM_ID'],
                    $arParams['IBLOCK_PROPS_IDS']['TYPE'] => $arParams['PROP_TYPE_IDS']['ANS'],
                    $arParams['IBLOCK_PROPS_IDS']['EMAIL'] => $_POST['email'],
                    $arParams['IBLOCK_PROPS_IDS']['NAME'] => $_POST['name'],
                    $arParams['IBLOCK_PROPS_IDS']['IP'] => $_SERVER['REMOTE_ADDR'],
                    $arParams['IBLOCK_PROPS_IDS']['ASK_ID'] => $_POST['askId']
                ],
                'NAME' => 'Ответ на вопрос: ' . $_POST['askId'] . ' (' . $_POST['name'] . ' [' . $_POST['email'] . '])'
            ];
            if($el->Add($arLoadProductArray)){
                echo json_encode((object)['TYPE' => 'OK', 'MSG' => 'Спасибо за ответ! Мы опубликуем его после модерации.']);
                die();
            } else {
                echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => 'Произошла ошибка :( Пожалуйста, сообщите текст ошибки консультанту в чат: ' . $el->LAST_ERROR]);
                die();
            }
        //}
    }

    if ($_POST['type'] == 'upd' /* || $_POST['type'] == 'sort' */) {
        $_GET = $_POST;
        global ${$_POST['params']['FILTER_NAME']};
        ${$_POST['params']['FILTER_NAME']} = $_POST['filter'];
        
        //if($_POST['type'] == 'sort'){

            if($_POST['sort'] == 'shows'){
                $sortField = 'property_RATING';
            } else {
                $sortField = 'ID';
            }

            $_POST['params']['SORT_BY1'] = $sortField;
            $_POST['params']['SORT_ORDER1'] = 'DESC';

            $_POST['params']['SORT_BY2'] = 'ID';
            $_POST['params']['SORT_ORDER2'] = 'DESC';
        //}

        $APPLICATION->IncludeComponent('bitrix:news.list', $_POST['template'], $_POST['params'], false);
    }

    if ($_POST['type'] == 'rate') {
        $propCode = 'LIKE';
        $rate = 'like';
        if($_POST['rate'] == 'dislike'){
            $propCode = 'DIS_LIKE';
            $rate = 'dislike';
        }
        
        if(!isset($_SESSION[$_SERVER['REMOTE_ADDR']][$_POST['ansId']])){
            //$propIdCount = $arParams['IBLOCK_PROPS_IDS'][$propCode];
            $rs = CIblockElement::GetList([], ['IBLOCK_ID' => $_POST['params']['IBLOCK_ID'], 'ID' => $_POST['ansId']], false ,false, ['ID', 'PROPERTY_' . $propCode]);
            if($ob = $rs->GetNext()){
                $curr = (int)$ob['PROPERTY_' . $propCode . '_VALUE'] + 1;
                CIBlockElement::SetPropertyValuesEx($_POST['ansId'], $_POST['params']['IBLOCK_ID'], array($propCode => $curr));
                $_SESSION[$_SERVER['REMOTE_ADDR']][$_POST['ansId']] = $propCode;

                $rsAns = CIblockElement::GetList([], ['IBLOCK_ID' => $_POST['params']['IBLOCK_ID'], 'PROPERTY_ASK_ID' => $_POST['askId']], false ,false, ['ID', 'PROPERTY_LIKE', 'PROPERTY_DIS_LIKE']);
                $arRate = ['LIKE' => 0, 'DIS_LIKE' => 0];
                while($obAsn = $rsAns->GetNext()){
                    $arRate['LIKE'] += (int)$obAsn['PROPERTY_LIKE_VALUE'];
                    $arRate['DIS_LIKE'] += (int)$obAsn['PROPERTY_DIS_LIKE_VALUE'];
                }
                if($arRate){
                    CIBlockElement::SetPropertyValuesEx($_POST['askId'], $_POST['params']['IBLOCK_ID'], array(
                        'LIKE' => $arRate['LIKE'],
                        'DIS_LIKE' => $arRate['DIS_LIKE'],
                        'RATING' => $arRate['LIKE'] - $arRate['DIS_LIKE']
                    ));
                }
                $ansId = (int)$_POST['ansId'];
                echo json_encode((object)['TYPE' => 'OK', 'COUNT' => $curr, 'typeRate' => 'rate', 'ansId' => $ansId, 'rate' => $rate]);
                die();
            }        
            
        }

        echo json_encode((object)['TYPE' => 'RATED']);
    }
}
