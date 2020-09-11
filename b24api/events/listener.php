<?
define( 'PORTAL_START_DEAL_ID', 163318 );
require_once("../includes/connection.php");
require_once("../includes/functions.php");
require_once("../includes/PortalData.php");

if (!in_array($_REQUEST['event'], array('ONCRMDEALADD'))) {
	die();
}
if ($_REQUEST['auth']['application_token'] != APP_TOKEN || $_REQUEST['auth']['member_id'] != PORTAL_MEMBER_ID || $_REQUEST['auth']['domain'] != PORTAL_ADDRESS) {
	die();
}

$deal_id = (int)$_REQUEST['data']['FIELDS']['ID'];
if ($deal_id) {
	executeMethod('crm.deal.update', [
		'id' => $deal_id,
		'fields' => [
			'UF_CRM_1568024869144' => '1',
		],
	]);
}
