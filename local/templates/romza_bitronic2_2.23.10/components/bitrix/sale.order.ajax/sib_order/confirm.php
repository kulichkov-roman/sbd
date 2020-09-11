<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
if (!empty($arResult["ORDER"])):
	$arUser = CUser::GetByID($USER->GetID())->Fetch();

	$arBasketItems = array();
	$arProductID = array();
	$arSection = array();
	CModule::IncludeModule('sale');
	$dbBasketItems = CSaleBasket::GetList(array(),
		array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => $arResult['ORDER']['ID']));
	while ($arItems = $dbBasketItems->Fetch()) {
		$arBasketItems[] = $arItems;
		$arProductID[] = $arItems['PRODUCT_ID'];
	}

	if(empty($arResult["DELIVERY"]["NAME"])){
		$order = \Bitrix\Sale\Order::loadByAccountNumber($arResult["ORDER"]["ACCOUNT_NUMBER"]);
		$ship = $order->getShipmentCollection();
		foreach($ship as $s)
			$arResult["DELIVERY"]["NAME"] = $s->getField("DELIVERY_NAME");
	}

	$arResult["ORDER"]["DISCOUNT_PRICE"] = $arResult["ORDER"]["DISCOUNT_PRICE"] >= 0 ? $arResult["ORDER"]["DISCOUNT_PRICE"] : 0;
?>
<div class="main-block main-block_order main-block_order-success">
	<?//global $USER; if(!$USER->IsAdmin()){echo '<pre>'; print_r($_SESSION['VREGIONS_REGION']); echo '</pre>';};?>
	<?include($_SERVER["DOCUMENT_ROOT"]."/include_areas/sib/order/confirm.php");?>
	<div class="total total_order">
		<img class="for-print margin-bottom-40" style="float:left" src="/bitrix/images/sibdroid/logo.jpg">
		<div class="for-print" style="float:right;max-width:40%;">
			<?=$_SESSION['VREGIONS_REGION']['TELEFON']?><br>
			<?=$_SESSION['VREGIONS_REGION']['ADRES']?>
		</div>
		<div class="clearfix"></div>
		<div class="total__heading">
			<?=GetMessage('RBS_YOUR_ORDER');?> <span class="rbs-order-for-print"><?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></span>
		</div>
		<ul class="order-list">
			<?foreach ($arBasketItems as $item):?>
				<li class="order-list__item">
					<?=$item["NAME"]?> <?if($item["QUANTITY"] > 1):?>(<?=$item["QUANTITY"]?>)<?endif?>
					<?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $item['PRICE'] * $item['QUANTITY'], $item['PRICE_FORMATED'], array('ID'=>'ITEM_PRICE_' . $item["ID"]))?>
				</li>
			<?endforeach;?>
			<?unset($item);?>
		</ul>
		<div class="total__delivery">
			<div class="total__heading">
				<?=GetMessage('RBS_YOUR_PAY_DELIVERY');?>
			</div>
			<?if($arResult["DELIVERY"]["NAME"]):?>
				<p><?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_DELIVERY');?> <span><?=!empty($_SESSION['TYPE_DELIVERY']) ? $_SESSION['DELIVERY_NAME_CONFIRM'] : 'Не выбрано'?></span></p>
			<?endif?>
        	<p><?=GetMessage('BITRONIC2_SOA_TEMPL_PAY');?> <span><?=$arResult["PAY_SYSTEM"]["NAME"]?></span></p>
		</div>
		<div class="total__info">
			<p><?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_DISCOUNT');?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult["ORDER"]["DISCOUNT_PRICE"], $arResult["ORDER"]["DISCOUNT_PRICE_FORMATED"], array('ID'=>'DISCOUNT_PRICE'))?></p>
        	<p><?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_DELIVERY_2');?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult['ORDER']['PRICE_DELIVERY'], $arResult["ORDER"]["DELIVERY_PRICE_FORMATED"], array('ID'=>'DELIVERY_PRICE'))?></p></p>
		</div>
		<div class="total__btm clearfix">
			<div class="total__sum">
				<?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_IT');?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult['ORDER']['PRICE'], $arResult['ORDER']['PRICE_FORMATED'], array('ID'=>'TOTAL_PRICE'))?>
			</div>
			<div class="total__btn">
				<a href="javascript:window.print();" class="button button_print"><?=GetMessage('RBS_PRINT');?></a>
			</div>
		</div> 
		<div class="for-print">
			<div>
				Ссылка на ваш заказ: <b>https://sibdroid.ru<?=$arParams["PATH_TO_PERSONAL"].'?ID='.$arResult["ORDER"]["ID"]?></b>
			</div>
		</div>
	</div>
</div>



<?/*ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/

if (!empty($arProductID)) {
	CModule::IncludeModule('iblock');
	$arSectionID = array();
	$arElementSectionID = array();
	$dbRes = CIBlockElement::GetList(array(), array('ID' => $arProductID), false, false,
		array('ID', 'IBLOCK_SECTION_ID'));
	while ($arRes = $dbRes->GetNext()) {
		$arElementSectionID[$arRes['ID']] = $arRes['IBLOCK_SECTION_ID'];
		$arSectionID[$arRes['IBLOCK_SECTION_ID']] = $arRes['IBLOCK_SECTION_ID'];
	}
	if (!empty($arSectionID)) {
		$dbRes = CIBlockSection::GetList(array(), array('ID' => $arSectionID), false, array('ID', 'NAME'));
		while ($arRes = $dbRes->GetNext()) {
			$arSection[$arRes['ID']] = $arRes;
		}
	}
}
?>
<script>
	dataLayer = dataLayer || [];
	dataLayer.push({

		'transactionId': '<?=$arResult['ORDER']['ID']?>',
		'transactionAffiliation': 'Sibdroid',
		'transactionTotal': <?=$arResult['ORDER']['PRICE']?>,
		'transactionTax': 0,
		'transactionShipping': <?=$arResult['ORDER']['PRICE_DELIVERY']?>,

		'transactionProducts': [
			<?$i = 1;foreach($arBasketItems as $item):?>
			{
				'name': '<?=$item['NAME']?>',
				'sku': '<?=$item['PRODUCT_ID']?>',
				'price': <?=$item['PRICE']?>,
				'category': '<?=(isset($arSection[$arElementSectionID[$item['PRODUCT_ID']]])) ? $arSection[$arElementSectionID[$item['PRODUCT_ID']]]['NAME'] : '0'?>',
				'quantity': <?=$item['QUANTITY']?>
			}<?if($i < count($item)):?>,<?endif;?>
			<?$i++;endforeach;?>
		]
	});
</script>
<?/*ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/
?>

<?endif;?>			
<?
// TODO stylized confirm order
if (false && !empty($arResult["ORDER"]))
{
	$arUser = CUser::GetByID($USER->GetID())->Fetch();
	?>
	<b><?=GetMessage("BITRONIC2_SOA_TEMPL_ORDER_COMPLETE")?></b><br /><br />
	<table class="sale_order_full_table">
		<tr>
			<td>
				<?= GetMessage("BITRONIC2_SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => '<a href="'.$arParams["PATH_TO_PERSONAL"].'?ID='.$arResult["ORDER"]["ID"].'" class="link"><span class="text">'.$arResult["ORDER"]["ACCOUNT_NUMBER"].'</span></a>'))?>
				<br /><br />
				<?= GetMessage("BITRONIC2_SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
				<?if (time()-300 < MakeTimeStamp($arUser['DATE_REGISTER'])):?>
				<br /><br />
				<?= GetMessage("BITRONIC2_SOA_TEMPL_ORDER_SUC2", Array("#LINK#" => $arParams["PATH_TO_SETTINGS"]))?>
				<?endif?>
			</td>
		</tr>
	</table>
    <?/*ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/
    $arBasketItems = array();
    $arProductID = array();
    $arSection = array();
    CModule::IncludeModule('sale');
    $dbBasketItems = CSaleBasket::GetList(array(),
        array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => $arResult['ORDER']['ID']));
    while ($arItems = $dbBasketItems->Fetch()) {
        $arBasketItems[] = $arItems;
        $arProductID[] = $arItems['PRODUCT_ID'];
    }
    if (!empty($arProductID)) {
        CModule::IncludeModule('iblock');
        $arSectionID = array();
        $arElementSectionID = array();
        $dbRes = CIBlockElement::GetList(array(), array('ID' => $arProductID), false, false,
            array('ID', 'IBLOCK_SECTION_ID'));
        while ($arRes = $dbRes->GetNext()) {
            $arElementSectionID[$arRes['ID']] = $arRes['IBLOCK_SECTION_ID'];
            $arSectionID[$arRes['IBLOCK_SECTION_ID']] = $arRes['IBLOCK_SECTION_ID'];
        }
        if (!empty($arSectionID)) {
            $dbRes = CIBlockSection::GetList(array(), array('ID' => $arSectionID), false, array('ID', 'NAME'));
            while ($arRes = $dbRes->GetNext()) {
                $arSection[$arRes['ID']] = $arRes;
            }
        }
    }
    ?>
    <script>
        dataLayer = dataLayer || [];
        dataLayer.push({

            'transactionId': '<?=$arResult['ORDER']['ID']?>',
            'transactionAffiliation': 'Sibdroid',
            'transactionTotal': <?=$arResult['ORDER']['PRICE']?>,
            'transactionTax': 0,
            'transactionShipping': <?=$arResult['ORDER']['PRICE_DELIVERY']?>,

            'transactionProducts': [
                <?$i = 1;foreach($arBasketItems as $item):?>
                {
                    'name': '<?=$item['NAME']?>',
                    'sku': '<?=$item['PRODUCT_ID']?>',
                    'price': <?=$item['PRICE']?>,
                    'category': '<?=(isset($arSection[$arElementSectionID[$item['PRODUCT_ID']]])) ? $arSection[$arElementSectionID[$item['PRODUCT_ID']]]['NAME'] : '0'?>',
                    'quantity': <?=$item['QUANTITY']?>
                }<?if($i < count($item)):?>,<?endif;?>
                <?$i++;endforeach;?>
            ]
        });
    </script>
    <?/*ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/
    ?>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<br /><br />

		<table class="sale_order_full_table">
			<tr>
				<td class="ps_logo">
					<div class="pay_name"><?=GetMessage("BITRONIC2_SOA_TEMPL_PAY")?></div>
					<?=CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0", "", false);?>
					<div class="paysystem_name"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></div><br>
				</td>
			</tr>
			<?
			if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
			{
				?>
				<tr>
					<td>
						<?
						if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
						{
							?>
							<script language="JavaScript">
								window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
							</script>
							<?= GetMessage("BITRONIC2_SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
							<?
							if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE']))
							{
								?><br />
								<?= GetMessage("BITRONIC2_SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
								<?
							}
						}
						else
						{
							if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
							{
								try
								{
									include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
								}
								catch(\Bitrix\Main\SystemException $e)
								{
									if($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
										$message = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
									else
										$message = $e->getMessage();

									echo '<span style="color:red;">'.$message.'</span>';
								}
							}
						}
						?>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
}
else if(false)
{
	?>
	<b><?=GetMessage("BITRONIC2_SOA_TEMPL_ERROR_ORDER")?></b><br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=GetMessage("BITRONIC2_SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))?>
				<?=GetMessage("BITRONIC2_SOA_TEMPL_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>
	<?
}
?>