<?header("Content-Type: text/plain");?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
echo str_replace("<br>", "", html_entity_decode($_SESSION["VREGIONS_REGION"]["ROBOTS_TXT"]["TEXT"]));?>