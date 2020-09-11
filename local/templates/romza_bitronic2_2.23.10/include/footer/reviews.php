<?use \Bitronic2\Mobile;?>
<section class="main-block main-block_reviews" id="rbs_footer_reviews_div">
    <?
        $dynamicArea = new \Bitrix\Main\Page\FrameStatic("rbs_footer_reviews");
        $dynamicArea->setContainerID("rbs_footer_reviews_div");
        $dynamicArea->startDynamicArea();
    ?>
        <p class="main-title"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/reviews/main_title.php")), false, array("HIDE_ICONS" => "N"));?></p>
        <?if ( !Mobile::isMobile() ):?>
            <div class="reviews reviews_desktop">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/reviews/flamp.php")), false, array("HIDE_ICONS" => "Y"));?>
                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/reviews/yandex.php")), false, array("HIDE_ICONS" => "Y"));?>
            </div>
        <?else:?>
            <div class="reviews reviews_mobile">
                <div class="tab-wrap">
                    <ul class="nav-tab-list tabs">
                        <li class="nav-tab-list__item active">
                            <a href="#tab_1" class="nav-tab-list__link">
                                <div class="reviews__image"><img alt="flamp" src="<?=SITE_TEMPLATE_PATH?>/new_img/flamp.png"></div>
                            </a>
                        </li>
                        <li class="nav-tab-list__item">
                            <a href="#tab_2" class="nav-tab-list__link rbs-ajax-yandex-mobile">
                                <div class="reviews__image"><img alt="yandex" src="<?=SITE_TEMPLATE_PATH?>/new_img/yandex.png"></div>
                            </a>
                        </li>
                    </ul>
                    <div class="box-tab-cont">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/reviews/flamp_mobile.php")), false, array("HIDE_ICONS" => "Y"));?>
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/reviews/yandex_mobile.php")), false, array("HIDE_ICONS" => "Y"));?>
                    </div>
                </div>
            </div>
        <?endif?>
    <?$dynamicArea->finishDynamicArea();?>
</section>