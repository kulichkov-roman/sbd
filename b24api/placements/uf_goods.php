<?
require_once("../includes/connection.php");
require_once("../includes/functions.php");
require_once("../includes/PortalData.php");

$placement = $_REQUEST['PLACEMENT'];
$placementOptions = isset($_REQUEST['PLACEMENT_OPTIONS']) ? json_decode($_REQUEST['PLACEMENT_OPTIONS'], true) : array();
$handler = ($_SERVER['SERVER_PORT'] === '443' ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
if(!is_array($placementOptions)) {
	$placementOptions = array();
}
if($placement === 'DEFAULT') {
	$placementOptions['MODE'] = 'edit';
}
//Array
//(
//    [MODE] => view
//    [ENTITY_ID] => CRM_DEAL
//    [FIELD_NAME] => UF_CRM_1568024869144
//    [ENTITY_VALUE_ID] => 75195
//    [VALUE] => 5056
//    [MULTIPLE] => N
//    [MANDATORY] => N
//    [XML_ID] =>
//)
$deal_id = $placementOptions['ENTITY_VALUE_ID'];

//Array
//(
//	[DOMAIN] => b24.sibdroid.ru
//		[PROTOCOL] => 1
//    [LANG] => ru
//[APP_SID] => aadb86fb325adec630d86a5568658f73
//[AUTH_ID] => bf37765d004005a00019f60f0000001b00000322317d4ae3240f4ae88263fe785be3a1
//[AUTH_EXPIRES] => 3600
//    [REFRESH_ID] => afb69d5d004005a00019f60f0000001b000003a8196949f95dfd0b430e2d6708501282
//[member_id] => 796300263a5ad5dd713c731505493bf5
//[status] => L
//[PLACEMENT] => USERFIELD_TYPE
//[PLACEMENT_OPTIONS] => {"MODE":"edit","ENTITY_ID":"CRM_DEAL","FIELD_NAME":"UF_CRM_1568024869144","ENTITY_VALUE_ID":"75157","VALUE":null,"MULTIPLE":"N","MANDATORY":"N","XML_ID":null}
//)

if ($_REQUEST['PLACEMENT'] == 'USERFIELD_TYPE' && $deal_id):
	$list = executeMethod('crm.deal.productrows.get', [
		'id' => $deal_id,
	]);
    $portal = new PortalData();
    $arDeal = $portal->getDeal($deal_id);
?><!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 14px; margin: 0; padding: 0; background-color: #f9fafb; }
        #products { width: 100%; border: 0; border-collapse: collapse; }
        #products tr td { border-bottom: 1px solid #e4e7ea; padding: 10px 0; }
        #products tr td.name { padding-right: 15px; }
        #products tr td.price { width: 150px; text-align: right; }
        #products tr:last-child td { border-bottom: none; font-weight: bold; }
    </style>
</head>
<body>
<table id="products">
<?if($list):?>
	<?foreach ($list as $item):?>
    <tr>
        <td class="name"><?=$item['PRODUCT_NAME'];?></td>
        <td class="quantity"><?=$item['QUANTITY'];?></td>
        <td class="price"><?=number_format($item['PRICE'], 0, ',', ' ');?> руб.</td>
    </tr>
	<?endforeach;?>
    <tr>
        <td>Итого:</td>
        <td></td>
        <td class="price"><?=number_format($arDeal['OPPORTUNITY'], 0, ',', ' ');?> руб.</td>
    </tr>
<?endif;?>
</table>
<script src="//api.bitrix24.com/api/v1/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var height = document.getElementById('products').clientHeight + 20;
        frame = BX24.getScrollSize();
        var width = frame.scrollWidth;
        BX24.resizeWindow(width, height);
    });
</script>
</body>
</html><?
endif;
