<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заполнение региона");

CModule::IncludeModule('iblock');

if(!empty($_POST))
{
    if((int)$_POST['elId'] == 0)
    {
        echo 'Не верно введен ID элементы';
    }
    elseif(empty($_POST['regions']))
    {
        echo 'Не выбраны регионы';
    }
    else
    {
        $dbEl = CIblockElement::GetList([], ['IBLOCK_ID' => 47, 'ID' => (int)$_POST['elId']]);
        if($dbEl->GetNext())
        {
            CIBlockElement::SetPropertyValuesEx((int)$_POST['elId'], 47, array('REGION_ID' => $_POST['regions']));
            echo 'Регионы установлены';
        }
        else
        {
            echo 'Не найден элемент';
        }
    }
    global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($_POST); echo '</pre>';};
}

?>
<div class="container">
    <div class="row">
        <form method="POST" action="">
            <div class="col-md-6">
                <label for="elId">ID Элемента</label>
                <input name="elId" type="text"/>
                <button class="btn btn-main" type="submit">Сохранить</button>
            </div>
            <div class="col-md-6">
                <label for="">Регионы</label>
                <?$dbRegions = CIblockElement::GetList(['NAME' => 'ASC'], ['IBLOCK_ID' => 46]);?>
                <select name="regions[]" id="" multiple="multiple" size="50" class="custom">
                    <?while($obRegions = $dbRegions->GetNext()):?>
                        <option value="<?=$obRegions['ID']?>" <?=in_array($obRegions['ID'], $_POST['regions'])?'selected':'';?>><?=$obRegions['NAME']?></option>
                    <?endwhile;?>
                </select>
            </div>
        </form>
    </div>
</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>