<?php
// Заказ клиента
$order = array(
    // Состав заказа
    'items' => array(
        array(
            'title' => 'Товар-1',
            'category' => 'Категория товара 1',
            'qty' => 1,
            'price' => 3500
        ),
        array(
            'title' => 'Товар-2',
            'category' => 'Категория товара 2',
            'qty' => 2,
            'price' => 1000
        ),
    ),
    // Информация о покупателе
    'details' => array(
        'firstname' => 'Иван',
        'lastname' => 'Иванов',
        'middlename' => 'Иванович',
        'email' => 'ivan@ivanov.com'
    ),
    'partnerId' => 'a06m00000018y7rAAA', // ID Партнера в системе Банка (выдается Банком)
    'partnerOrderId' => 'test_order_'.uniqid(), // Уникальный номер заказа в системе Партнера
);

// JSON-представление заказа
$json = json_encode($order);

// Base64-кодирование JSON-представления заказа
$base64 = base64_encode($json);

// Секретная строка для формирования подписи (выдается Банком)
$secret = 'grid-secret-18y7r72a';

/**
 * Функция формирования подписи заказа
 * @param $message Base64-представление заказа
 * @param $secretPhrase Секретная строка
 * @return string
 */
function signMessage($message, $secretPhrase) {
    $message = $message.$secretPhrase;
    $result = md5($message).sha1($message);
    for ($i = 0; $i < 1102; $i++) {
        $result = md5($result);
    }
    return $result;
}

// Формирование подписи
$sign = signMessage($base64, $secret);
?>

<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Пример интеграции системы КупиВкредит</title>
    <script src="https://form-test.kupivkredit.ru/sdk/v1/sdk.js?onload=myOnLoadFunction" type="text/javascript" async></script>
    <script type="text/javascript">
        window.callbacks = [];

        window.onload = function() {
            for (var i = 0; i < this.callbacks.length; i++) {
                this.callbacks[i].call();
            }
        };

        window.myOnLoadFunction = function(KVK) {
            var button, form;
            form = KVK.ui("form", {
                order:"<?php echo $base64; ?>",
                sign: "<?php echo $sign; ?>",
                type: "full"
            });

            window.callbacks.push(function() {
                button = document.getElementById("open");
                button.removeAttribute("disabled");
                button.onclick = function() {
                    // Открытие формы по нажатию кнопки
                    form.open();
                };
            });
        }

    </script>
</head>
<body>
Нажмите для открытия формы <input type="button" id="open" name="open" value="Открыть">
</body>
</html>