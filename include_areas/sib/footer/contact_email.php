<?
$_SESSION['VREGIONS_REGION']['Email'] = $_SESSION['VREGIONS_REGION']['Email'] ? : 'sales@sibdroid.ru';
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($_SESSION['VREGIONS_REGION']['Email']); echo '</pre>';};
?>
<a class="footer-mail" href="mailto:<?=$_SESSION['VREGIONS_REGION']['Email']?>"><?=$_SESSION['VREGIONS_REGION']['Email']?></a>