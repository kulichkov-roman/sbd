<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER; if(!$USER->IsAdmin()){LocalRedirect('/');}
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");

\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('sib.core');

$groupList = \Bitrix\Catalog\GroupTable::getList()->fetchAll();

$groupSelect = [];
foreach($groupList as $group){
    $groupSelect[$group['ID']] = "[{$group['ID']}] {$group['NAME']}";
}


if($_POST){
 
    $saveParam = [];
    foreach($_POST['PRICE_TYPES'] as $pType => $pValue){
        if($pValue !== 'N')
            $saveParam[] = "{$pType}:{$pValue}";
    }
    \COption::SetOptionString('sib.core', 'price_equals', implode('|', $saveParam)); 
    $saveParam = [];
    foreach($_POST['PRICE_STICKERS'] as $pType => $pValue){
        if (!empty($pValue['CODE']) && !empty($pValue['VALUE'])) {
            $pValue = implode('#', $pValue);
            $saveParam[] = "{$pType}:{$pValue}";
        }
    }
    \COption::SetOptionString('sib.core', 'price_stickers', implode('|', $saveParam)); 
}

$default = \Sib\Core\Prices::getPriceEquals();
$defaultStickers = \Sib\Core\Prices::getPriceStickers();
?>
 
 <main style="padding: 60px;">

    <form method="POST" action="">

    <h3>Настройка цены</h3>
        <?foreach($groupList as $group):?>
           <div style="margin-bottom: 15px;">
                <span><?=$group['NAME']?></span>
                <select name="PRICE_TYPES[<?=$group['ID']?>]" id="">
                    <option value="N">
                        Не выбрано
                    </option>
                    <?foreach($groupSelect as $groupKey => $groupOption):?>
                        <option value="<?=$groupKey?>" <?=$default[$group['ID']] == $groupKey ? 'selected' : ''?>>
                            <?=$groupOption?>
                        </option>
                    <?endforeach?>
                </select>
           </div>
        <?endforeach?>

        <h3>Настройка стикеров</h3> 
        <ul>
            <li>Код свойства -- свойства типа список, куда будем ставить значение стикера. В списке значений должно быть только одно значение - Y</li>
            <li>Значение свойства -- ID значения варианат этого свойства Y. Можно увидеть левеее внешнего кода самого значения свойства. <a href="https://yadi.sk/i/hBSe4nODM4MBGw" target="_blank">Скриншот где его искать</a></li>
        </ul>
        <?foreach($groupList as $group):?>
            <div style="margin-bottom: 15px;">
                <b><?=$group['NAME']?></b><br>
                <label for="">Код свойства</label>
                <input class="input" type="text" name="PRICE_STICKERS[<?=$group['ID']?>][CODE]" value="<?=$defaultStickers[$group['ID']]['CODE']?>">
                <label for="">Значение свойства</label>
                <input class="input" type="text" name="PRICE_STICKERS[<?=$group['ID']?>][VALUE]" value="<?=$defaultStickers[$group['ID']]['VALUE']?>"> 
           </div>
           <hr>
        <?endforeach?>

        <h3>Настройка минимальной цены</h3>  
        <p>Здесь нужно создать свойство типа "ЧИСЛО" с кодом свойства SIB_MIN_PRICE_BASE, где BASE -- имя типа цены</p>

        <input class="button" type="submit" value="Сохранить">

    </form>

</main>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>