<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER; if(!$USER->IsAdmin()){LocalRedirect('/');}
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");

$APPLICATION->SetAdditionalCSS("/new_css/style_1.css");

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sib.core');
\Bitrix\Main\Loader::includeModule('sproduction.integration');

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

if(!empty($_POST) && (int)$_GET['profile'] > 0){
    if(!empty($_POST['regions'])){
        $prof = \SProduction\Integration\ProfilesTable::getById($_GET['profile']);
        $filters = $prof['filter']['filter'];
        
        foreach($filters as &$filter){
            if($filter['field'] == 'prop_20'){
                $filter['value'] = $_POST['regions'];
                break;
            }
        }
        \SProduction\Integration\ProfilesTable::update($_GET['profile'], ['filter' => [
            'filter' => $filters
        ]]);
        //CIBlockElement::SetPropertyValuesEx($_GET['profile'], 47, ['REGION_ID' => $propReg]);
    }
}

$profiles = \SProduction\Integration\ProfilesTable::getList();
//global $USER; if($USER->IsAdmin()){echo '<pre>';print_r($profiles);echo '</pre>';}
?>
<div class="main-block">
    <form action="">
        <select name="profile">
            <option value="0">Выберите профиль</option>
            <?foreach($profiles as $profile):?>
                <option value="<?=$profile['id']?>" <?=$_GET['profile'] == $profile['id']?'selected':''?>><?=$profile['name']?></option>
            <?endforeach?>
        </select>
        <script>
            $('select[name="profile"]').on('change', function(){
                $(this).closest('form').submit();
            });
        </script>
    </form>
    <br><br>
    <?if((int)$_GET['profile'] > 0):?>
        <?
            $prof = \SProduction\Integration\ProfilesTable::getById($_GET['profile']);
            $filters = $prof['filter']['filter'];
            //global $USER; if($USER->IsAdmin()){echo '<pre>';print_r($filters);echo '</pre>';}

            $regionFilterVals = [];
            foreach($filters as $filter){
                if($filter['field'] == 'prop_20'){
                    $regionFilterVals = $filter['value'];
                    break;
                }
            }
        ?>
        <form name="" method="POST" action="" style="display:flex">
            <div style="width:30%">
                <select name="regions[]" id="" multiple style="height:400px;">
                    <?foreach($obRegs as $obReg):?>
                        <option data-price="<?=$regionPrice[$obReg['ID']]?>" value="<?=$obReg['ID']?>" <?=in_array($obReg['ID'], $regionFilterVals)?'selected':''?>><?=$obReg['NAME']?></option>
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
                <br><br>
                <input class="button" type="submit" value="Сохранить">
            </div> 
        </form>
    <?endif?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>