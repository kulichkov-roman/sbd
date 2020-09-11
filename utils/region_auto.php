<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER; if(!$USER->IsAdmin()){LocalRedirect('/');}
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");

$APPLICATION->SetAdditionalCSS("/new_css/style_1.css");

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sib.core');

$rsRegions = CIblockElement::GetList(['NAME' => 'ASC'], ['IBLOCK_ID' => 46], false, false, []);
$rsRegionProps = CIblockProperty::GetList(['PROPERTY_TYPE' => 'ASC', 'SORT' => 'ASC'], ['IBLOCK_ID' => 46]);

$obRegs = [];
$regionPrice = [];
while($obReg = $rsRegions->GetNext()){
    $obRegs[$obReg['ID']] = $obReg;
    $db_props = CIBlockElement::GetProperty(46, $obReg['ID'], array("sort" => "asc"), Array("CODE"=>"PRICE_CODE"));
    if($ar_props = $db_props->Fetch()){
        $regionPrice[$obReg['ID']] = $ar_props['VALUE'];
    }
}

$groupList = \Bitrix\Catalog\GroupTable::getList()->fetchAll();
$groupSelect = [];
foreach($groupList as $group){
    $groupSelect[$group['ID']] = "[{$group['ID']}] {$group['NAME']}";
}

$unsetProps = [
    'EDOST_CURR_DAY',
    'EDOST_WEEKEND',
    'CHOSEN_ONE',
    'EDOST_CUSTOM_PRICE_31',
    'CENTR_REGIONA'
];

if(!empty($_POST)){
    $arAllNotEmptyProps = [];
    $isNeedUpdatePropCatalog = false;
    foreach($_POST['PROPS'] as $propType => $props){
        foreach($props as $propCode => $propVal){
            if($propType == 'L'){
                if($propVal != 'N'){
                    $arAllNotEmptyProps[$propCode] = $propVal;
                    $isNeedUpdatePropCatalog = true;
                }
            } else {
                if(!empty($propVal)){
                    if(is_array($propVal)){
                        $isEmpty = true;
                        foreach($propVal as $propValEx){
                            if(!empty($propValEx['VALUE'])){
                                $isEmpty = false;
                                break;
                            }
                        }
                        if(!$isEmpty){
                            $arAllNotEmptyProps[$propCode] = $propVal;
                        }
                    } else {
                        $arAllNotEmptyProps[$propCode] = $propVal;
                    }
                }
            }
        }
    }
    
    if(!empty($_POST['regions']) && count($arAllNotEmptyProps) > 0){
        foreach($_POST['regions'] as $regionId){
            if($isNeedUpdatePropCatalog){
                $propCode = 'SIB_AVAIL_' . $regionId;
                \Sib\Core\Catalog::createAvailProp($propCode, $obRegs[$regionId]['NAME']);
            }
            CIBlockElement::SetPropertyValuesEx($regionId, 46, $arAllNotEmptyProps);
        }
    }
}
?>
<div class="main-block">
    <form name="" method="POST" action="" style="display:flex">
        <div style="width:30%">
            <select name="regions[]" id="" multiple style="height:200px;">
                <?foreach($obRegs as $obReg):?>
                    <option data-price="<?=$regionPrice[$obReg['ID']]?>" value="<?=$obReg['ID']?>"><?=$obReg['NAME']?></option>
                <?endforeach?>
            </select>

            <select name="price_type" id="">
                <option value="N">Выделить города по типу цены</option>
                <?foreach($groupList as $group):?>
                    <option value="<?=$group['NAME']?>"><?=$group['NAME']?></option>
                <?endforeach?>
            </select>
            <script>
                $('[name="price_type"]').on('change', function(){
                    if($(this).val() !== 'N'){
                        $('[data-price="'+$(this).val()+'"]').attr('selected', 'selected');
                    }
                });
            </script>
            <br>
            <input class="button" type="submit">
        </div>
       <div style="width:70%">
            <?while($obProp = $rsRegionProps->GetNext()):?>
                <?if(in_array($obProp['CODE'], $unsetProps)) continue;?>
                <div style="margin: 10px; padding: 10px; border:1px solid #dadada">
                    <label for=""><?=$obProp['NAME']?></label>
                    <?if($obProp['PROPERTY_TYPE'] == 'S'):?>
                        <?if($obProp['MULTIPLE'] == 'N'):?>
                            <input type="text" name="PROPS[S][<?=($obProp['CODE'])?>]" class="input">
                        <?else:?>
                            <?for($i = 0; $i < 5; $i++):?>
                                <input type="text" name="PROPS[S][<?=($obProp['CODE'])?>][<?=$i?>][VALUE]" class="input">
                            <?endfor?>
                        <?endif?>
                    <?elseif($obProp['PROPERTY_TYPE'] == 'E'):?>
                        <input type="text" name="PROPS[E][<?=($obProp['CODE'])?>]" class="input">
                    <?elseif($obProp['PROPERTY_TYPE'] == 'L'):?>
                        <select name="PROPS[L][<?=($obProp['CODE'])?>]">
                        <option value="N">Не выбрано</option>
                        <?
                            $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>46, "CODE"=>$obProp['CODE']));
                            while($enum_fields = $property_enums->GetNext())
                            {?>
                                <option value="<?=$enum_fields["ID"]?>"><?=$enum_fields["VALUE"]?></option>
                            <?}
                        ?>
                        </select>
                    <?endif?>
                </div>
            <?endwhile?>
       </div>
        
    </form>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>