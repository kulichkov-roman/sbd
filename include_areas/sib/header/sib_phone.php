<?use Bitronic2\Mobile;
$method = 'tel';
$phonePart = explode(')', $_SESSION["VREGIONS_REGION"]["TELEFON"]);
if(count($phonePart) < 2){
    $phonePart = $_SESSION["VREGIONS_REGION"]["TELEFON"];
} else {
    $phonePart = '<span>' . $phonePart[0] . ')</span> ' . $phonePart[1];
}
$standartTime = '<span>06:00-17:00</span>мск';
if(\Bitrix\Main\Loader::includeModule('sib.core')){
    if(\Sib\Core\Catalog::isMskRegion()){
        $standartTime = '<span>10:00-21:00</span>';
    } else if(\Sib\Core\Catalog::isDefRegion()){
        $standartTime = '<span>09:00-21:00</span>';
    }
}
?>
<div class="header-phone js-click">
    <button class="header-phone__button js-click-button"></button>

    <a class="header-phone__number" itemprop="telephone" href="<?=$method?>:<?echo $_SESSION["VREGIONS_REGION"]["TELEFON"]?>"><?echo $phonePart?></a>
    
    <div class="callback js-click-hide shadow-default">
        <button class="close-default button-close js-click-close"></button>
        <div class="call-form">
            <? include $_SERVER['DOCUMENT_ROOT'] . '/include_areas/sib/footer_sib/callme.php'; ?>
        </div>
        <p class="callback__title">Время приема звонков: <b><?=$standartTime?></b></p>
    </div>
</div>