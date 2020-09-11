<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $isNewTemplate;
$APPLICATION->SetPageProperty("keywords", "");
$APPLICATION->SetPageProperty("title", "О магазине Sibdroid.ru");
$APPLICATION->SetTitle("О магазине");

if($isNewTemplate):
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
?><section class="main-block">
 
 #VREGION_TEXT_BY_URL#
 <? \Yenisite\Core\Tools::IncludeArea('about', 'reviews', false, true) ?><br>
 <br>
 </section>
<?else:?> <main class="container about-page">
<h1></h1>
<h1>Sibdroid.ru – это низкие цены на смартфоны и аксессуары к ним, с быстрой доставкой по всей России!&nbsp;<br>
 </h1>
<ul type="disc">
	<li>Мы одержимы качественным сервисом, довольный клиент - вот наша цель!</li>
	<li>Понимаем, что вам хочется получить свою покупку как можно скорее. Поэтому собираем и отправляем товар в течение 15 минут после подтверждения заказа. </li>
	<li>Знаем буквально все о смартфонах и с удовольствием делимся этими знаниями с вами. У неопытного пользователя регулярно возникают вопросы по работе техники, отвечаем на них там, где вам удобно: в чате на сайте, в группе VK, по бесплатной линии 8 800.&nbsp;</li>
	<li>Дорожим нашей репутацией и всегда даем обратную связь на отзывы о нашей работе на Yandex.Market и Flamp.ru. Интересные идеи записываем и внедряем в работу.&nbsp;</li>
</ul>
<p>
 <img width="100%" alt="office.jpg" src="/about/office.jpg" height="100%" title="office.jpg" align="middle"><br>
</p>
<p>
 <b>Адрес:</b> г. Новосибирск, ул. Новогодняя 17, вход с улицы Новогодняя <br>
 <b>Режим работы:</b> пн-вс с 09:00 по 21:00<br>
 <b>Телефоны</b><b>:</b><br>
	 8 (383) 383-00-55 (телефон в Новосибирске)<br>
	 8 (800) 333-55-87 (звонок из регионов России бесплатный)<br>
</p>
 <? \Yenisite\Core\Tools::IncludeArea('about', 'reviews', false, true) ?><br>
 <br>
 </main>
<?endif?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>