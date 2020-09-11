<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");

global $USER;
if (CModule::IncludeModule('developx.gcaptcha')){
    $captchaObj = new Developx\Gcaptcha\Main();
    if ($captchaObj->checkCaptcha()){
        if($USER->IsAdmin()){echo '<pre>';print_r(['its ok']);echo '</pre>';}
    } else {
        if($USER->IsAdmin()){echo '<pre>';print_r(['its false']);echo '</pre>';}
    }
}

?>
    <form action="">

        <input type="text" name="test">
    
        <?
            $APPLICATION->IncludeComponent(
                "developx:gcaptcha",
                "",
                Array(),
                false
            );
        ?>
    
        <button type="submit">Send</button>

    </form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>