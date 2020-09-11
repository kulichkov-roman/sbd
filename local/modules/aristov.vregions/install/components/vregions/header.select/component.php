<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

CJSCore::Init(array('ajax')); // zashhita ot govnoshablona
\Bitrix\Main\Page\Asset::getInstance()->addJs($this->GetPath()."/script.js");

$subdomainCookie = $APPLICATION->get_cookie("VREGION_SUBDOMAIN");

$arParams["IBLOCK_ID"] = \Aristov\VRegions\Tools::getModuleOption("vregions_iblock_id");

$arParams["SORT_BY1"]            = trim($arParams["SORT_BY1"]) ?: (\Aristov\VRegions\Tools::getModuleOption("header_select_sort_by1") ?: 'SORT');
$arParams["SORT_BY2"]            = trim($arParams["SORT_BY2"]) ?: (\Aristov\VRegions\Tools::getModuleOption("header_select_sort_by2") ?: 'NAME');
$arParams["SORT_ORDER1"]         = trim($arParams["SORT_ORDER1"]) ?: (\Aristov\VRegions\Tools::getModuleOption("header_select_sort_order1") ?: 'DESC');
$arParams["SORT_ORDER2"]         = trim($arParams["SORT_ORDER2"]) ?: (\Aristov\VRegions\Tools::getModuleOption("header_select_sort_order2") ?: 'ASC');
$arParams["CACHE_TIME"]          = intval($arParams["CACHE_TIME"] ?: (\Aristov\VRegions\Tools::getModuleOption("vregions_iblock_id") ?: 3600));
$arParams["INCLUDE_PROPS_ARRAY"] = trim($arParams["INCLUDE_PROPS_ARRAY"]) ?: (\Aristov\VRegions\Tools::getModuleOption("header_select_include_props_array") ?: 'N');

if ($arParams['INCLUDE_SESSION_ARRAY_IN_CACHE'] != 'N'){
    $arParams["CURRENT_SESSION_ARRAY"] = $_SESSION["VREGIONS_REGION"];
}

if (!\AristovVregionsHelper::isDemoEnd()){
    if ($this->StartResultCache()){
        if (!CModule::IncludeModule("iblock")){
            $this->AbortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));

            return;
        }
        if (!CModule::IncludeModule("aristov.vregions")){
            $this->AbortResultCache();
            ShowError(GetMessage("VREGIONS_MODULE_NOT_INSTALLED"));

            return;
        }

        $arResult                          = array(
            "ITEMS" => array(),
        );
        $arResult["CURRENT_SESSION_ARRAY"] = $arParams["CURRENT_SESSION_ARRAY"];

        $VREGION_DEFAULT = COption::GetOptionString("aristov.vregions", "vregions_default");

        $rs = CIBlockElement::GetList(
            array(
                $arParams["SORT_BY1"] => $arParams["SORT_ORDER1"],
                $arParams["SORT_BY2"] => $arParams["SORT_ORDER2"],
                "ID"                  => "DESC",
            ),
            array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ACTIVE"    => "Y"
            ),
            false,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "NAME",
                "CODE",
                "PROPERTY_HTTP_PROTOCOL",
                "PROPERTY_FULL_URL",
                "PROPERTY_CHOSEN_ONE",
                "PROPERTY_OBLAST",
                "PROPERTY_WWW",
                "PROPERTY_DO_NOT_SHOW_IN_COMPONENT",
            )
        );
        while($ob = $rs->GetNextElement()){
            $arFields = $ob->GetFields();
            if ($arParams['INCLUDE_PROPS_ARRAY'] == 'Y'){
                $arFields["PROPERTIES"] = $ob->GetProperties();
            }

            if ($arFields['PROPERTY_DO_NOT_SHOW_IN_COMPONENT_VALUE'] == 'Y'){
                continue;
            }

            if ($arParams["CURRENT_SESSION_ARRAY"]){
                if ($arFields["CODE"] == $arParams["CURRENT_SESSION_ARRAY"]["CODE"]){
                    $arFields["CLASS"]  = "active";
                    $arFields["ACTIVE"] = "true";
                }
            }

            $arFields["HREF"] = Aristov\Vregions\Tools::generateRegionLink($arFields["CODE"], $arFields["PROPERTY_HTTP_PROTOCOL_VALUE"], $arFields["PROPERTY_FULL_URL_VALUE"], $arFields["PROPERTY_WWW_VALUE"]);

            if ($arFields["CODE"] == $VREGION_DEFAULT){
                $arResult["DEFAULT"] = $arFields;
            }

            if ($arFields["CODE"] == $VREGION_DEFAULT){
                $arFields["CODE"] = "";
            }

            $arFields['CHOSEN_ONE'] = $arFields['PROPERTY_CHOSEN_ONE_VALUE'];
            $arFields['OBLAST']     = $arFields['PROPERTY_OBLAST_VALUE'];

            $arResult["ITEMS"][] = $arFields;
        }

        $this->IncludeComponentTemplate();
    }
}