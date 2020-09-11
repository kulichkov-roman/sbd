<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="success__box">
    <div class="thanks-box">
        <div class="thanks-box__title">Спасибо! Ваш заказ <a href="<?=$arParams["PATH_TO_PERSONAL"].'?ID='.$arResult["ORDER"]["ID"]?>">№<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></a> сформирован</div>
        <div class="thanks-box__subtitle">Менеджер свяжется с вами в ближайшее время.<br> Если у вас остались вопросы, звоните на бесплатную линию <a href="tel:88003335587">8 800 333-55-87</a></div>
        <div class="thanks-box__subtitle"><strong>Вы зарегистрированы и авторизованы.</strong><br> На указанный при оформлении заказа e-mail отправлена ссылка для смены пароля.<br> Также пароль можно сменить в <a href="/personal/">настройках профиля.</a></div>
    </div>

    <div class="banner-box">
        <div class="banner-box__text">Теперь вы можете участвовать в ежемесячных акциях для постоянных покупателей магазина!</div>
        <div class="banner-box__btn">
            <a href="https://sibdroid.ru/actions/market_reviews.html" class="button banner_button">Подробнее</a>
        </div>
    </div>

    <div class="benefit-box">
        <div class="benefit-box__title">Получите множество преимуществ, зарегистрировавшись на сайте!</div>
        <ul class="benefit__list">
            <li class="benefit__item">
                <i class="benefit__item-icon icon-geolocation"></i>
                <div class="benefit__item-title">Отслеживайте текущие заказы</div>
                <div class="benefit__item-subtitle">Удобная и оперативная информация о вашем заказе в личном кабинете.</div>
            </li>
            <li class="benefit__item">
                <i class="benefit__item-icon icon-speed-figuration"></i>
                <div class="benefit__item-title">Ускоренное оформление будущих покупок</div>
                <div class="benefit__item-subtitle">Ваши контактные данные и использованные ранее настройки заполняются автоматически.</div>
            </li>
            <li class="benefit__item">
                <i class="benefit__item-icon icon-percent1"></i>
                <div class="benefit__item-title">Персональные акции и скидки</div>
                <div class="benefit__item-subtitle">Специальные предложения для зарегистрированных пользователей.</div>
            </li>
        </ul>
        <div class="benefit-box__btn">
            <a href="/personal/" class="button login_button">Войти в личный кабинет</a>
        </div>
        <div class="benefit-box__subscribe">
            Подпишитесь на наши социальные сети:
        </div>
        <div class="social-wrap">
            <div class="social">
                <ul class="social-list">
                    <!--
                    <li class="social-list__item">
                        <a href="#" class="social-list__link socials-2__link socials-2__link_icon-1"></a>
                    </li>
                    <li class="social-list__item">
                        <a href="#" class="social-list__link socials-2__link socials-2__link_icon-2"></a>
                    </li>
                    <li class="social-list__item">
                        <a href="#" class="social-list__link socials-2__link socials-2__link_icon-3"></a>
                    </li>
                    <li class="social-list__item">
                        <a href="#" class="social-list__link socials-2__link socials-2__link_icon-4"></a>
                    </li>
                    <li class="social-list__item">
                        <a href="#" class="social-list__link socials-2__link"><span class="icon-ok"></span></a>
                    </li>
                    -->
                    <li class="social-list__item">
                        <a href="https://www.instagram.com/sibdroid/" target="_blank" class="social-list__link socials-2__link socials-2__link_icon-3"></a>
                    </li>
                    <li class="social-list__item">
                        <a href="https://vk.com/sibdroid" target="_blank" class="social-list__link socials-2__link socials-2__link_icon-2"></a>
                    </li>
                    <li class="social-list__item">
                        <a href="https://www.youtube.com/channel/UCBimjgo8woDwT3gX-ntehlQ" target="_blank" class="social-list__link socials-2__link socials-2__link_icon-4"></a>
                    </li>
                    <li class="social-list__item">
                        <a href="https://www.facebook.com/sibdroid/" target="_blank" class="social-list__link socials-2__link socials-2__link_icon-1"></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="benefit-box__btn">
            <a href="/catalog/" class="button catalog_button">Перейти в каталог</a>
        </div>
    </div>
</div>