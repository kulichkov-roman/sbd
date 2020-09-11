<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER; if(!$USER->IsAdmin()){LocalRedirect('/');}
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
?>
<div class="main-block">
    Нужно выполнить код в командной строке
    <br>
   <pre>
   CModule::IncludeModule('iblock');

    $rs = CIblockElement::GetList([], ['IBLOCK_ID' => 46]);

    while($element = $rs->GetNextElement()){
        $arFields = $element->GetFields();
        $arProps = $element->GetProperties();


        $arSetVals = [];

        //здесь ставим условие из какого тарифа в какой копируем, в данном случае из 37 в 46, аналогично можно любые цифры проставить
        if(
            is_array($arProps['EDOST_CUSTOM_PRICE_37']['VALUE']) &&
            count($arProps['EDOST_CUSTOM_PRICE_37']['VALUE']) > 0
        ){
            foreach($arProps['EDOST_CUSTOM_PRICE_37']['VALUE'] as $k => $val){
                $arSetVals['EDOST_CUSTOM_PRICE_46'][$k]['VALUE'] = $val;
            }
        }

        if(
            is_array($arProps['EDOST_CUSTOM_PRICE_38']['VALUE']) &&
            count($arProps['EDOST_CUSTOM_PRICE_38']['VALUE']) > 0
        ){
            foreach($arProps['EDOST_CUSTOM_PRICE_38']['VALUE'] as $k => $val){
                $arSetVals['EDOST_CUSTOM_PRICE_47'][$k]['VALUE'] = $val;
            }
        }

        if(count($arSetVals) > 0)
            \CIblockElement::SetPropertyValuesEx($arFields['ID'], 46, $arSetVals);

    }
   </pre>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>