<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
// TODO stylized confirm order
if (!empty($arResult["ORDER"]))
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
else
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
