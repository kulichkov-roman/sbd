<?
$_SERVER["DOCUMENT_ROOT"] = $DOCUMENT_ROOT = realpath(dirname(__FILE__)."/..");

require_once("includes/connection.php");
require_once("includes/functions.php");
require_once("includes/PortalData.php");


/*$deal_id = 75195;

$portal = new PortalData();
$arDeal = $portal->getDeal($deal_id);

$list = $portal->getList('crm.deal.list', '', ['=UF_CRM_1568024869144' => '', '!STAGE_ID' => ['LOSE','C2:LOSE','C3:LOSE','WON','C2:WON','C3:WON']], ['ID', 'STAGE_ID'], ['ID'=>'desc'], 100);
echo '<pre>'; print_r($list); echo '</pre>';
foreach ($list as $item) {
    executeMethod('crm.deal.update', [
        'id' => $item['ID'],
        'fields' => [
            'UF_CRM_1568024869144' => '1',
        ],
    ]);
}*/

/*
$list = executeMethod('crm.deal.productrows.get', [
	'id' => $deal_id,
]);
?><!DOCTYPE html>
<html>
<head>
	<style>
		body { font-family: sans-serif; font-size: 14px; margin: 0; padding: 0; background-color: #f9fafb; }
		.products { width: 100%; border: 0; border-collapse: collapse; }
		.products tr td { border-bottom: 1px solid #e4e7ea; padding: 10px 0; }
		.products tr:last-child td { border-bottom: none; }
	</style>
</head>
<body>
<?if($list):?>
	<table class="products" >
		<?foreach ($list as $item):?>
		<tr>
			<td><?=$item['PRODUCT_NAME'];?></td>
			<td><?=$item['QUANTITY'];?></td>
			<td><?=$item['PRICE'];?> руб.</td>
		</tr>
		<?endforeach;?>
		<tr>
			<td>Итого:</td>
			<td></td>
			<td class="price"><?=number_format($arDeal['OPPORTUNITY'], 0, ',', ' ');?> руб.</td>
		</tr>
	</table>
<?endif;?>
</body>
</html>
*/