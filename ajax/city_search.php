<?
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

$return = (object)['COUNT' => 0];
if(!empty($_REQUEST['q']) && strlen($_REQUEST['q']) >= 2 && \Bitrix\Main\Loader::includeModule('iblock'))
{
                                                                    //46
    $db = \CIblockElement::GetList(['NAME' => 'asc'], ['IBLOCK_ID' => 46, '%NAME' => $_REQUEST['q'], 'ACTIVE' => 'Y'], false, false, ['NAME', 'CODE']);
    
    $arElements = [];
    while($el = $db->GetNext())
    {
        $arElements[] = (object)$el;
    }

    $return = (object)['COUNT' => $db->SelectedRowsCount(), 'ITEMS' => (object)$arElements];
}

echo json_encode($return);