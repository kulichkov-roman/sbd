<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $isNewTemplate;

use Bitrix\Main\Page\Asset;

$APPLICATION->SetPageProperty("description", "Работаем ежедневно с 9:00 по 21:00 по Новосибирску. Наш телефон 8(383) 383-00-55");
$APPLICATION->SetPageProperty("keywords", "контакты, sibdroid");
$APPLICATION->SetPageProperty("title", "Контакты интернет-магазина Sibdroid.ru");
$APPLICATION->SetTitle("Контакты ");

if($isNewTemplate):
//$APPLICATION->AddHeadString('<script src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU"></script>');

/* if(!empty($_SESSION["VREGIONS_REGION"]["CENTR_REGIONA"])){
	$APPLICATION->AddHeadString('<script>var regionCenter = ['.$_SESSION["VREGIONS_REGION"]["CENTR_REGIONA"].']</script>');
	$APPLICATION->AddHeadString('<script>var regionAddress = "'.$_SESSION["VREGIONS_REGION"]["ADRES"].'"</script>');
} */

$asset = Asset::getInstance();
$asset->addCss(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/rbs-scripts/contacts.js");
?>
<!-- <h1><?$APPLICATION->ShowTitle()?></h1> -->
	
	<aside class="contacts-info">
	    <ul class="contacts-tabs-list nav-tab-list tabs">
	        <li class="contacts-tabs-list__item active">
	            <a href="#tab_1" class="contacts-tabs-list__link">Контакты</a>
	        </li>
	        <li class="contacts-tabs-list__item">
	            <a href="#tab_2" class="contacts-tabs-list__link">Написать нам</a>
	        </li>
	    </ul>
	    <div class="contacts-tabs-cont contacts">
	        <div class="contacts-tabs-cont__item" id="tab_1">
				#VREGION_TEXT_BY_URL_NEW#
	            <ul class="contacts-social">
	                <li>
	                    <a href="https://www.instagram.com/sibdroid/" target="_blank" class="contacts-social__item contacts-social__item_inst"></a>
	                </li>
	                <li class="">
	                    <a href="https://vk.com/sibdroid" target="_blank" class="contacts-social__item contacts-social__item_vk"></a>
	                </li>
	                <li>
	                    <a href="https://www.youtube.com/channel/UCBimjgo8woDwT3gX-ntehlQ" target="_blank" class="contacts-social__item contacts-social__item_youtube"></a>
	                </li>
	               <!--  <li>
	                    <a href="https://www.facebook.com/sibdroid/" target="_blank" class="contacts-social__item contacts-social__item_facebook"></a>
	                </li> -->
	            </ul>
	        </div>
	        <div class="contacts-tabs-cont__item hide" id="tab_2">
	            <?$APPLICATION->IncludeComponent(
					"yenisite:feedback.add", 
					"rbs_sib", 
					array(
						"ACTIVE" => "Y",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_ADDITIONAL" => "",
						"AJAX_OPTION_HISTORY" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "Y",
						"AJAX_REDIRECT" => "Y",
						"CACHE_TIME" => "300",
						"CACHE_TYPE" => "A",
						"COLOR_SCHEME" => "green",
						"ELEMENT_ID" => "",
						"EMAIL" => "EMAIL",
						"EVENT_NAME" => "FEEDBACK",
						"IBLOCK" => "3",
						"IBLOCK_TYPE" => "bitronic2_feedback",
						"NAME" => "NAME",
						"NAME_FIELD" => "NAME",
						"PHONE" => "PHONE",
						"PRINT_FIELDS" => array(
							0 => "NAME",
							1 => "EMAIL",
							2 => "TEXT"
						),
						"SECTION_CODE" => "",
						"SHOW_SECTIONS" => "N",
						"SUCCESS_TEXT" => "",
						"TEXT_REQUIRED" => "N",
						"TEXT_SHOW" => "N",
						"TITLE" => "",
						"USE_CAPTCHA" => "N",
						"COMPONENT_TEMPLATE" => "rbs_sib"
					),
					false
				);?>
	        </div>
	    </div>
	</aside>
	<div class="contacts-main">
        <div class="contacts-map">
			<div id="map" style="width: 100%; height: 100%">
			#VREGION_TEXT_MAP_NEW#
			</div>
        </div>
	</div>
<?else:?>
<main class="container about-page">
<h1><?$APPLICATION->ShowTitle()?></h1>
#VREGION_TEXT_BY_URL#
</main>
<?endif?>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>