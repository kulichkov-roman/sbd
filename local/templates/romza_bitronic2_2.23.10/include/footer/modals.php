<?
use Bitronic2\Mobile;
$arDefIncludeParams = array(
    "AREA_FILE_SHOW" => "file",
    "EDIT_TEMPLATE" => "include_areas_template.php"
);
if (CModule::IncludeModule('yenisite.oneclick')):?>
    <div class="popup-fast-order" id="modal_quick-buy" tabindex="-1">
        <div class="popup__main">
            <div class="popup__title">Быстрый заказ</div>
            <div class="form"></div>
        </div>
    </div>
    <div class="popup-fast-order" id="modal_credit" tabindex="-1">
        <div class="popup__main">
            <div class="popup__title">Купить в кредит</div>
            <div class="form"></div>
        </div>
    </div>
<?endif?>
<div class="popups">
    <? \Yenisite\Core\Tools::IncludeArea('sib/footer_sib', 'modal_subscribe', false, false);?>
    <div class="popup-subscribe-form" id="popup-subscribe-form" style="display: none;">
        <div class="popup__header"><?= GetMessage('BITRONIC2_SUBSCRIBE_FORM_TITLE') ?></div>
        <div class="popup__line"></div>
        <div class="popup__content"></div>
    </div>
    <div class="popup-accessories fancybox-content" id="popup-accessories" style="display: none;">
        <div class="popup__main"></div>
    </div>
    <div class="popup-accessories fancybox-content" id="popup-contacts" style="display: none;">
        <div class="popup__main">
            <div class="popup__title"></div>
            <div class="form">
                <div id="popup-map" class="map" style="width:100%; height:450px;">

                </div>
            </div>
        </div>
    </div>

    <div class="popup-fast-order" id="modal_msg_uni" style="display: none;">
        <div class="popup__main">
            <div class="popup__title"></div>
            <div class="form"></div>
        </div>
    </div>

    <div class="popup popup__add_to_cart" id="modal_add_to_cart" style="display: none;">
        <div class="popup__main">
            <div class="popup__title">Товар добавлен в корзину</div>
            <div class="form">

                <div data-fancybox-close class="button button_white">Продолжить покупки</div>
                <a href="/personal/cart/" class="button">Перейти в корзину</a>

            </div>
        </div>
    </div>
    
    <div class="popup" id="popup_forgot_pass" style="display: none;">
        <div class="popup__main">
            <div class="popup__title">Восстановление пароля</div>
            <div class="form">
                <div class="info-text">После заполнения формы мы отправим ссылку, перейдя по которой вы сможете задать новый пароль</div><br>
                <form id="forgot_pass_ajax" class="rbs-forgot-pass-form" action="">
                    <div><input class="input" placeholder="Адрес электронной почты" type="email" name="forgot_email" required></div>
                    <div class="info"></div>
                    <div><button class="login-form__button button button_white">Отправить</button></div>
                </form>
            </div>
        </div>
    </div>
</div>