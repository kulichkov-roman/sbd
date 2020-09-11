<?use Bitronic2\Mobile;
$method = 'tel';
$phonePart = explode(')', $_SESSION["VREGIONS_REGION"]["TELEFON"]);
if(count($phonePart) < 2){
    $phonePart = $_SESSION["VREGIONS_REGION"]["TELEFON"];
} else {
    $phonePart = '<span>' . $phonePart[0] . ')</span> ' . $phonePart[1];
}
?>
<a class="footer-tel" itemprop="telephone" href="<?=$method?>:<?echo $_SESSION["VREGIONS_REGION"]["TELEFON"]?>"><?=$phonePart?></a>