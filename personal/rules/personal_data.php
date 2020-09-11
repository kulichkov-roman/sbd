<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Согласие на обработку персональных данных");
$asset = Bitrix\Main\Page\Asset::getInstance();
$asset->addCss(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
?>
    <section class="main-block">
        <h1><? $APPLICATION->ShowTitle() ?></h1>
        <p>Пользователь, оформляя заявку на сайте https://sibdroid.ru/ (далее – Сайт), соглашается с условиями
            настоящего Согласия на обработку персональных данных (далее — Согласие) в соответствии со ст. 9
            Федерального закона от 27.07.2006 № 152-ФЗ «О персональных данных». Принятием (акцептом) оферты
            Согласия является отправка заявки с Сайта. </p>
        <p>Пользователь дает свое согласие ИП Белокопытова Юлия Олеговна (далее - Оператор), который
            находится по адресу: Россия, 630064, г. Новосибирск, ул. Новогодняя, 17 (тел. 8 (800) 333-55-87), на
            обработку своих персональных данных со следующими условиями:
        </p>
        <p>1. Данное Согласие дается на обработку персональных данных как без, так и с использованием средств
            автоматизации. </p>
        <p>2. Согласие распространяется на следующую информацию: фамилия, имя, отчество, телефон, электронная
            почта.
        </p>
        <p>3. Согласие на обработку персональных данных дается в целях предоставления Пользователю ответа на
            заявку, дальнейшего заключения и выполнения обязательств по договорам, осуществления клиентской
            поддержки, информирования об услугах, которые, по мнению Оператора, могут представлять интерес для
            Пользователя, проведения опросов и маркетинговых исследований.
        </p>
        <p>4. Пользователь, предоставляет Оператору право осуществлять следующие действия (операции) с
            персональными данными: сбор, запись, систематизация, накопление, хранение, уточнение (обновление,
            изменение), использование, обезличивание, блокирование, удаление и уничтожение, передача третьим
            лицам, с согласия субъекта персональных данных и соблюдением мер, обеспечивающих защиту персональных
            данных от несанкционированного доступа. </p>
        <p>6. Персональные данные обрабатываются Оператором до завершения всех необходимых процедур. Также
            обработка может быть прекращена по запросу Пользователя на электронную почту:
            <a href="mailto:mail@sibdroid.ru" class="contacts-email">
                <span itemprop="email">mail@sibdroid.ru</span>
            </a>
        </p>
        <p>7. Пользователь подтверждает, что, давая Согласие, он действует свободно, своей волей и в своем
            интересе. </p>
        <p>8. Настоящее Согласие действует бессрочно до момента прекращения обработки персональных данных по
            причинам, указанным в п.6 данного документа.</p>
            
    </section>
<? require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php" ?>