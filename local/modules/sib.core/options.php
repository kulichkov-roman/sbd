<?
/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CDatabase $DB
 */

use \Bitrix\Main\Loader;
use Bitrix\Sale\Location;
 
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");

if(!$USER->CanDoOperation('view_other_settings') && !$USER->CanDoOperation('edit_other_settings'))
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

//check modules
/* if(!Loader::IncludeModule('iblock')){echo GetMessage('MODULE_IBLOCK_ERROR'); return false;}
if(!Loader::IncludeModule('catalog')){echo GetMessage('MODULE_CATALOG_ERROR'); return false;} */
if(!Loader::IncludeModule('sale')){echo GetMessage('MODULE_SALE_ERROR'); return false;}

$errorList = [];

$mid = $_REQUEST["mid"]?:'sib.core';

$deliveries = [
    296 => 'EMS Почта России',
    297 => 'EMS Почта России (со страховкой)',
    352 => 'Самовывоз из пункта выдачи СДЭК',
    354 => 'Курьером до двери СДЭК',
    370 => 'Самовывоз из пункта выдачи DPD',
    372 => 'Курьером до двери DPD',
    348 => 'Самовывоз',
    340 => 'Курьер',
    346 => 'Курьер (рядом с МСК)',
    344 => 'Курьер (Одинцово)',
    342 => 'Курьер (рядом с НСК)',
    406 => 'СДЭК (магистральный/экономичный - курьером до двери)',
    404 => 'СДЭК (магистральный/экономичный - до пункта выдачи)',
    423 => 'Курьер Отдаленная Мск',
    350 => 'Boxberry (пункт выдачи)',
    364 => 'Boxberry (курьер)',
    351 => 'Boxberry (пункт выдачи страховка)',
    365 => 'Boxberry (курьер страховка)',
    371 => 'DPD  (пункт выдачи страховка)',
    373 => 'DPD (курьер страховка)',
 	353 => 'Транспортной компанией СДЭК до пункта выдачи в Вашем городе',
    355 => 'Транспортной компанией СДЭК курьером до двери',
];

$rsDeliveries = CSaleDelivery::GetList(["SORT" => "ASC","NAME" => "ASC"], ["ACTIVE" => "Y"]);
//echo $rsDeliveries->SelectedRowsCount();
while($obDelivery = $rsDeliveries->Fetch()){
    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($obDelivery); echo '</pre>';};
    $deliveries[$obDelivery['ID']] = $obDelivery['NAME'];
}


$arAllOptions = [
    "main" => [
        "Настройки доставок",
        ["sib_core_edost_self", "Доставки самовывоза", "", ["multiselectbox", $deliveries]],
        ["sib_core_edost_courier", "Курьерские доставки", "", ["multiselectbox", $deliveries]],
    ],
    /* "catalog" => [
        GetMessage('IT_HEAD_CATALOG'),
        ["bot_catalog_iblock", GetMessage("IT_CATALOGIBLOCK"), '', ['selectbox', $iblocks]],
		["bot_catalog_currency", GetMessage("IT_CATALOGCURRENCY"), '', ['selectbox', $currencies]],
		

        GetMessage('SECTION_LIST'),
        ["bot_catalog_sort_section", GetMessage("IT_CATALOGSORTSECTION"), '', ['selectbox', $sortSection]],
        ["bot_catalog_by_section", GetMessage("IT_CATALOGBYSECTION"), '', ['selectbox', $by]],
        ["bot_catalog_pagecount_section", GetMessage("IT_CATALOGPAGECOUNTSECTION"), '9', ['selectbox', $pageCount]],
        ["bot_catalog_twice_section", GetMessage("IT_CATALOGTWICESECTION"), 'N', ['checkbox', 'N']],
        ["bot_catalog_count_section", GetMessage("IT_CATALOGCOUNTSECTION"), 'N', ['checkbox', 'N']],

        GetMessage('SECTION'),
        ["bot_catalog_sort_element", GetMessage("IT_CATALOGSORTELEMENT"), '', ['selectbox', $sortElement]],
        ["bot_catalog_by_element", GetMessage("IT_CATALOGBYELEMENT"), '', ['selectbox', $by]],
        ["bot_catalog_pagecount_element", GetMessage("IT_CATALOGPAGECOUNTELEMENT"), '9', ['selectbox', $pageCount]],
        ["bot_catalog_hidenotavail_element", GetMessage("IT_CATALOGHIDENOTAVAILELEMENT"), 'N', ['checkbox', 'N']],
		["bot_catalog_show_only_goods", GetMessage("IT_CATALOGSHOWONLYGOODS"), 'N', ['checkbox', 'N']],

        GetMessage('ELEMENT'),
        ["bot_catalog_element_descr", GetMessage("IT_CATALOGELEMENTDESCR"), 'DETAIL_TEXT', ['selectbox', $descrFields]],
        ["bot_catalog_element_htmlsplit", GetMessage("IT_CATALOGELEMENTHTMLSPLIT"), 'N', ['checkbox', 'N']],
        ["bot_catalog_element_photofields", GetMessage("IT_CATALOGELEMENTPHOTOFIELD"), 'DETAIL_PICTURE', ['selectbox', array_merge(['NOT' => GetMessage('NOT')], $photoFields)]],
        ["bot_catalog_element_photoprop", GetMessage("IT_CATALOGELEMENTPHOTOPROP"), 'MORE_PHOTO', ['selectbox', array_merge(['NOT' => GetMessage('NOT')], $arProperty_F)]],
        ["bot_catalog_element_props", GetMessage("IT_CATALOGELEMENTPROPS"), '', ["multiselectbox", $props]],
    ],
    "order" => [
        GetMessage("IT_HEAD_ORDER"),
        ["bot_order_person_type", GetMessage("IT_ORDERPTYPE"), "", ['selectbox', $personType]],
        ["bot_order_props", GetMessage("IT_ORDERPROPS"), "", ['multiselectbox', $ordProps]],
        ["bot_order_props_phone", GetMessage("IT_ORDERPROPSPHONE"), "", ['selectbox', $phoneProps]],
		["bot_order_props_loc", GetMessage("IT_ORDERPROPSLOC"), "", ['selectbox', $locProps]],
		["bot_order_props_address", GetMessage("IT_ORDERPROPSADDRESS"), "", ['selectbox', $phoneProps]],
        ["bot_order_delivery", GetMessage("IT_ORDERDELIVERY"), "", ['multiselectbox', $delivery]],
        ["bot_order_paysystem", GetMessage("IT_ORDERPAYSYSTEM"), "", ['multiselectbox', $paysystem]],
        ["bot_order_component", GetMessage("IT_ORDERCOMPONENT"), "", ['text', 30]],
    ] */
];

$arOptionsForClass = [];
foreach($arAllOptions as $key=>$section)
{
    foreach($section as $option)
    {
        if(is_array($option) && !empty($option[0]))
            $arOptionsForClass[$key][$option[0]] = $option[2];
    }

}


if($_SERVER["REQUEST_METHOD"] == "POST" && $USER->IsAdmin() && check_bitrix_sessid())
{
    //COption::RemoveOption($mid);
    foreach($arAllOptions as $key=>$section)
    {
        foreach($section as $option)
        {
            if(is_array($option) && !empty($option[0]))
            {
                if(is_array($_REQUEST[$option[0]]))$_REQUEST[$option[0]] = implode(',', $_REQUEST[$option[0]]);
                COption::SetOptionString($mid, $option[0], $_REQUEST[$option[0]]);
            }
        }

    }
    //echo '<pre>';print_r($_REQUEST);echo '</pre>';
}



//var_export($arOptionsForClass);

//tabs

$aTabs = [
    [
        "DIV" => "edit1",
        "TAB" => "Основные настройки",
        "ICON" => "main_settings",
        "TITLE" => "Настройки edost"
    ],
   /*  [
        "DIV" => "edit2",
        "TAB" => GetMessage("IT_TAB_CATALOG"),
        "ICON" => "catalog_settings",
        "TITLE" => GetMessage("IT_TAB_CATALOG")
    ],
    [
        "DIV" => "edit3",
        "TAB" => GetMessage("IT_HEAD_ORDER"),
        "ICON" => "order_settings",
        "TITLE" => GetMessage("IT_HEAD_ORDER")
    ] */
];

$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByArray($arParams, $mid = 'sib.core')
{
    foreach($arParams as $Option)
    {
        if(is_array($Option) && $Option[0] !== "" && $Option[2] !== "")
        {
            $optStr = COption::GetOptionString($mid, $Option[0], $Option[2]);
            if($optStr == NULL)
                COption::SetOptionString($mid, $Option[0], $Option[2]);
        }
        __AdmSettingsDrawRow($mid, $Option);
    }
}
?>
<form name="main_options" method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&amp;lang=<?echo LANG?>">
<?=bitrix_sessid_post()?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();

    ShowParamsHTMLByArray($arAllOptions["main"]);
/* 
$tabControl->BeginNextTab();
   
    ShowParamsHTMLByArray($arAllOptions["catalog"]);

$tabControl->BeginNextTab();
   
    ShowParamsHTMLByArray($arAllOptions["order"]); */
    
$tabControl->Buttons();?>

<script type="text/javascript">
function RestoreDefaults()
{
    if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
        window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>&<?echo bitrix_sessid_get()?>";
}
</script>
<?//if($_REQUEST["back_url_settings"] <> ""):?>
    <input <?if (!$USER->CanDoOperation('edit_other_settings')) echo "disabled" ?> type="submit" name="Save" value="<?echo GetMessage("MAIN_SAVE")?>" title="<?echo GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
<?//endif?>
<?/*?>
<input <?if (!$USER->IsAdmin()) echo "disabled" ?> type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="back_url_settings" value="<?echo htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
<?*/?>
<?$tabControl->End();?>

</form>