<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("showSubscribe", "Y");
$APPLICATION->SetTitle("Личный кабинет");
if (!$USER->IsAuthorized()) {
	$APPLICATION->AuthForm("");
	return;
}
global $isNewTemplate;
if($isNewTemplate):
?>
<h2 class="account-page-title"><? $APPLICATION->ShowTitle() ?></h2>
<? include '../left_menu.php'; ?>
<? if (CModule::IncludeModule('subscribe')): ?>
    <div class="personal-account-main personal-account-main_settings">
        <? $APPLICATION->IncludeComponent("bitrix:subscribe.edit", "sib_default", Array(
            "AJAX_MODE" => "N",
            "SHOW_HIDDEN" => "N",
            "ALLOW_ANONYMOUS" => "Y",
            "SHOW_AUTH_LINKS" => "Y",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "SET_TITLE" => "N",
            "AJAX_OPTION_SHADOW" => "Y",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
        ),
            false
        ); ?>
    </div>
<? endif ?>

<?else:?>
    <main class="container account-page account-settings">
		<h1><? $APPLICATION->ShowTitle() ?></h1>
		<div class="account row">
			<? include '../left_menu.php'; ?>
			<? if (CModule::IncludeModule('subscribe')): ?>
			<div class="account-content col-xs-12 col-sm-9 col-xl-10">
				<? $APPLICATION->IncludeComponent("bitrix:subscribe.edit", ".default", Array(
					"AJAX_MODE" => "N",
					"SHOW_HIDDEN" => "N",
					"ALLOW_ANONYMOUS" => "Y",
					"SHOW_AUTH_LINKS" => "Y",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"SET_TITLE" => "N",
					"AJAX_OPTION_SHADOW" => "Y",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
				),
					false
				); ?>
			</div>
		</div>
		<? endif ?>
	</main>
<?endif?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>