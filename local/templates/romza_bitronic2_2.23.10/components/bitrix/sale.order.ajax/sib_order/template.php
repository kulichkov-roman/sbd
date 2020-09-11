<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Page\Asset;
global $rz_b2_options;
$arDefIncludeParams = array(
    "AREA_FILE_SHOW" => "file",
    "EDIT_TEMPLATE" => "include_areas_template.php"
);
if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y"){
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)		{
			$APPLICATION->RestartBuffer();?>
				<script type="text/javascript">
					window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
				</script>
			<?die();
		}
	}
}

if(empty($_COOKIE['activeBlock'])){
	$_COOKIE['activeBlock'] = 'props';
}

$_SESSION['CAN_ORDER'] = false;

if(!empty($_POST['delivery_type'])){
	$_SESSION['TYPE_DELIVERY'] = $_POST['delivery_type'];
} else {
	if(!empty($_COOKIE['ORDER_PROP_1']) && !empty($_COOKIE['ORDER_PROP_2']) && !empty($_COOKIE['ORDER_PROP_3'])){
		$_SESSION['CAN_ORDER'] = true;
	}
}

if($_SESSION['TYPE_DELIVERY']){
	$_SESSION['CAN_ORDER'] = false;
	if($_COOKIE['activeBlock'] == 'pay'){
		$_SESSION['CAN_ORDER'] = true;
	}
}

CJSCore::Init(array('fx', 'popup', 'window', 'ajax'));
//Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/pages/initOrderDetailsPage.old.js");
global $activeChoose;
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($_COOKIE); echo '</pre>';};
//echo $_COOCKIE['activeBlock'];
?>

<style>
.main-block_order-success{align-items: flex-start;}
.main-block_order .order-list__item span{width:100px;}
.delivery-options-list .icon-rub, span.b-rub{display:none;}
.rbs-delivery-price-item span.b-rub{display:inline;}
.order-list__item span{text-align:right;}
.payment-item img, .delivery-options__img img{width:auto;margin:auto;}
.main-block_order .total{max-height:100%}

.rbs-delivery-type{margin-bottom: 15px;}
.rbs-delivery-type__type{display: inline-block;width: 176px;padding: 10px;border: 2px solid #cccccc;text-align: center;color:#222;cursor: pointer;margin-right: 6px;}
.rbs-delivery-type__type:hover, .rbs-delivery-type__active{border-color:#ffce24;}
.rbs-delivery-type__head{font-size: 14px;font-weight: bold;}
.rbs-delivery-type__descr{font-size: 10px;}

.order__cnt{position:relative;}
#ajaxLoader{
	display:none;
	position: absolute;
	z-index: 999;

	background:url(<?=SITE_TEMPLATE_PATH?>/new_css/ajax-transparent.gif);
	background-position: 50% 50%;
	background-repeat: no-repeat;

	width: 100%;
    height: 100%;
    
    top: -50px;
    left: -50px;
}

#ajaxLoaderMask{
	display:none;
	position: absolute;
	z-index: 998;

	background-color:rgba(255,255,255,0.5);

	width: 100%;
    height: 100%;
    
    top: 0;
    left: 0;
}

.rbs-need-address {
	display:none;
	padding: 10px;
    background: #ea6767;
    color: #fff;
    border-radius: 5px;
    margin-bottom: 10px;
}
@media screen and (max-width: 640px){
	.payment-item img, .delivery-options__img img{width:auto;margin:auto;}
}
@media screen and (max-width: 479px){
	.rbs-delivery-type,.order-delivery-town{text-align:center;}
	.rbs-delivery-type__type{width:142px;}
}
</style>
<?if(!$USER->IsAuthorized()):?>
<div class="hide">
	<? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/user_auth.php")), false, array("HIDE_ICONS" => "Y")); ?> 
</div>
<?endif?>
<div id="mask" class="mask-inner"></div>
<?//echo $_COOKIE['activeBlock'];?>
<section class="main-block main-block_order" data-page="order-details-page" id="order_form_div">
	
	<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/func.php");?>
	<?
	if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N"){
		if(!empty($arResult["ERROR"])){
			foreach($arResult["ERROR"] as $v) {
				CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => $v, "TYPE" => "ERROR"));
			}
		}elseif(!empty($arResult["OK_MESSAGE"])){
			foreach($arResult["OK_MESSAGE"] as $v)
				CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => $v, "TYPE" => "OK"));
		}
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
	} else {
		if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")	{
			if(strlen($arResult["REDIRECT_URL"]) == 0) {
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
			}
		} else {
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/script_submit.php");
			if($_POST["is_ajax_post"] != "Y"){
		?>
				<form action="<?=$APPLICATION->GetCurPage();?>" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data" style="width:100%">
					
					<?=bitrix_sessid_post()?>
					<div class="rbs-flex-desktop-box" id="order_form_content"> 
		<?	} else {$APPLICATION->RestartBuffer();}?>
						<div class="order"> 
							<? // echo $_POST['isDeliveryDone']; ?>
							<h3 class="main-title"><?$APPLICATION->ShowTitle()?></h3>
							<NOSCRIPT>
								<div class="errortext"><?=GetMessage("BITRONIC2_SOA_NO_JS")?></div>
							</NOSCRIPT>
							<?
								
								if(!isset($_SESSION['ORDER_CLASSES']) || $_COOKIE['activeBlock'] == 'props'){
									$_SESSION['ORDER_CLASSES']['prop'] = 'active';
									$_SESSION['ORDER_CLASSES']['delivery'] = '';
									$_SESSION['ORDER_CLASSES']['pay'] = '';
								} else {
									if($_COOKIE['activeBlock'] == 'delivery'){
										$_SESSION['ORDER_CLASSES']['prop'] = 'order-ok';
										$_SESSION['ORDER_CLASSES']['delivery'] = 'active';
										$_SESSION['ORDER_CLASSES']['pay'] = '';
									}
									if($_COOKIE['activeBlock'] == 'pay'){
										$_SESSION['ORDER_CLASSES']['prop'] = 'order-ok';
										$_SESSION['ORDER_CLASSES']['delivery'] = 'order-ok';
										$_SESSION['ORDER_CLASSES']['pay'] = 'active';
									}
								}
							?>
							<div class="order__cnt">
								<div id="ajaxLoader"></div>
								<div id="ajaxLoaderMask"></div>
								<div class="order-step">
									<div class="order-step__item <?=$_SESSION['ORDER_CLASSES']['prop']?>" data-block-name="props">
										<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");?>
									</div>
									<div class="order-step__item <?=$_SESSION['ORDER_CLASSES']['delivery']?>" data-block-name="delivery">
										<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");?>
									</div>
									<div class="order-step__item <?=$_SESSION['ORDER_CLASSES']['pay']?>" data-block-name="pay">
										<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");?>
									</div>
								</div>
							</div>
						</div>
						<div class="total_order-wrap">	
							<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/total.php");?>
						</div>	
			<?if($_POST["is_ajax_post"] != "Y"){?>
					</div><!-- .order_form_content -->
						<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
						<input type="hidden" name="profile_change" id="profile_change" value="N">
						<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
						<input type="hidden" name="json" value="Y">
						<?if(in_array($_COOKIE['activeBlock'], ['delivery', 'props', 'pay'])):?>
							<script>
								var firstAvailDelivery = $('[data-delivery-type="self"]:first-child [name="DELIVERY_ID"]:first-child');
								if(firstAvailDelivery.length){
									firstAvailDelivery.click();
								} else {
									$('[name="DELIVERY_ID"]:first-child').click();
								}
								
							</script>
						<?endif?>
				</form><?
					if($arParams["DELIVERY_NO_AJAX"] == "N"){
						?><div style="display:none;"><?$APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?></div><?
					}
			} else {?>
				<script type="text/javascript">
					top.BX('confirmorder').value = 'Y';
					top.BX('profile_change').value = 'N';
				</script>
				<?die();
			}
		}
	}
	?>
</section>
<?if(CSaleLocation::isLocationProEnabled()):?>
	<div style="display: none">
		<?// we need to have all styles for sale.location.selector.steps, but RestartBuffer() cuts off document head with styles in it?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.location.selector.steps", 
			".default", 
			array(
			),
			false
		);?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.location.selector.search", 
			".default", 
			array(
			),
			false
		);?>
	</div>
<?endif;?>