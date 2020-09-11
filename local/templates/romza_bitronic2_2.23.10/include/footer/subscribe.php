<section class="subscribe<?if ($isPersonalPage):?> subscribe-contacts<?endif?>">
    <div class="subscribe__main-wrap wrapper">
        <div class="subscribe__main">
            <div class="subscribe__cols">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/subscribe.php")), false, array("HIDE_ICONS" => "Y"));?>
            </div>
        </div>
    </div>
</section>