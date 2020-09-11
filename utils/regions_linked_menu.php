<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER; if(!$USER->IsAdmin()){LocalRedirect('/');}
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");

$APPLICATION->SetAdditionalCSS("/new_css/style_1.css");

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sib.core');

$rsRegions = CIblockElement::GetList(['NAME' => 'ASC'], ['IBLOCK_ID' => 46]);

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

if(!empty($_POST) && (int)$_GET['topmenu'] > 0){
    if(is_array($_POST['regions']) && count($_POST['regions']) > 0){
        if($_POST['type_saving'] === 'save'){
            $propReg = [];
            foreach($_POST['regions'] as $regionId){
                $propReg[] = ['VALUE' => $regionId];
            }
            CIBlockElement::SetPropertyValuesEx($_GET['topmenu'], 47, ['REGION_ID' => $propReg]);
        } else if($_POST['type_saving'] === 'delete'){
            $props =  CIBlockElement::GetList([], ['IBLOCK_ID' => 47, 'ID' =>$_GET['topmenu']])->GetNextElement()->GetProperties();
            $propReg = [];
            if(is_array($props['REGION_ID']['VALUE']) && count($props['REGION_ID']['VALUE']) > 0){
                foreach($props['REGION_ID']['VALUE'] as $regionId){
                    if(!in_array($regionId, $_POST['regions'])){
                        $propReg[] = ['VALUE' => $regionId];
                    }
                }
            }
            if(count($propReg) <= 0){
                $propReg = false;
            }
            CIBlockElement::SetPropertyValuesEx($_GET['topmenu'], 47, ['REGION_ID' => $propReg]);
        }
        
    }
}

$rsTopMenu = CIblockElement::GetList(['IBLOCK_SECTION_ID' => 'ASC'], ['IBLOCK_ID' => 47, 'SECTION_ID' => 939, 'INCLUDE_SUBSECTIONS' => 'Y']);
$arMenuItems = [];
$arSecNames = [];
while($obMenu = $rsTopMenu->GetNext()){
    $arMenuItems[$obMenu['IBLOCK_SECTION_ID']][] = [
        'ID' => $obMenu['ID'],
        'NAME' => $obMenu['NAME']
    ];

    $obSec = CIblockSection::GetById($obMenu['IBLOCK_SECTION_ID'])->GetNext();
    $arSecNames[$obMenu['IBLOCK_SECTION_ID']] = $obSec['NAME'];
}


?>
<div class="main-block">
    <form action="">
        <select name="topmenu">
            <?foreach($arMenuItems as $sectionId => $items):?>
                <optgroup label="<?=$arSecNames[$sectionId]?>">
                    <?foreach($items as $item):?>
                        <option value="<?=$item['ID']?>" <?=$_GET['topmenu'] == $item['ID']?'selected':''?>><?=$item['NAME']?></option>
                    <?endforeach?>
                </optgroup>
            <?endforeach?>
        </select>
        <script>
            $('select[name="topmenu"]').on('change', function(){
                $(this).closest('form').submit();
            });
        </script>
    </form>
    <br><br>
    <?if((int)$_GET['topmenu'] > 0):?>
        <?
            $currentProp = array();
            $res = CIBlockElement::GetProperty(47, $_GET['topmenu'], "sort", "asc", array("CODE" => "REGION_ID"));
            while ($ob = $res->GetNext()){
                $currentProp[] = $ob['VALUE'];
            }
            //global $USER; if($USER->IsAdmin()){echo '<pre>';print_r($currentProp);echo '</pre>';}
        ?>
        <form name="" method="POST" action="" style="display:flex">
            <div style="width:30%">
                <select name="regions[]" id="" multiple style="height:400px;">
                    <?foreach($obRegs as $obReg):?>
                        <option data-price="<?=$regionPrice[$obReg['ID']]?>" value="<?=$obReg['ID']?>" <?=in_array($obReg['ID'], $currentProp)?'selected':''?>><?=$obReg['NAME']?></option>
                    <?endforeach?>
                </select>
                <br>
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
                <div>Тип сохранения</div>
                <select name="type_saving" id="">
                    <option value="save">Сохранить города</option>
                    <option value="delete">Удалить города</option>
                </select>
                <br><br>
                <input class="button" type="submit" value="Сохранить">
            </div> 
        </form>
    <?endif?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>