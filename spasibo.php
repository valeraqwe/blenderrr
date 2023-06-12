<?php
session_start();
// формируем массив с товарами в заказе (если товар один - оставляйте только первый элемент массива)
$products_list = array(
    0 => array(
        'product_id' => 220,    //код товара (из каталога CRM)
        'price'      => $_REQUEST['product_price'], //цена товара 1
        'count'      => '1',                     //количество товара 1
    ),
    1 => array(
        'product_id' => 89,    //код товара 2 (из каталога CRM)
        'price'      => $_REQUEST['product_price'], //цена товара 2
        'count'      => '1',                     //количество товара 2
    )

);
$products = urlencode(serialize($products_list));
$sender = urlencode(serialize($_SERVER));
// параметры запроса
$data = array(
    'key'             => '52cba41c92b9dac5e40be43d415752bd', //Ваш секретный токен
    'order_id'        => number_format(round(microtime(true)*10),0,'.',''), //идентификатор (код) заказа (*автоматически*)
    'country'         => 'UA',                         // Географическое направление заказа
    'office'          => '1',                          // Офис (id в CRM)
    'products'        => $products,                    // массив с товарами в заказе
    'bayer_name'      => $_REQUEST['name'],            // покупатель (Ф.И.О)
    'phone'           => $_REQUEST['phone'],           // телефон
    'email'           => $_REQUEST['email'],           // электронка
    'comment'         => $_POST['comment'],    // комментарий
    'delivery'        => $_REQUEST['delivery'],        // способ доставки (id в CRM)
    'delivery_adress' => $_REQUEST['delivery_adress'], // адрес доставки
    'payment'         => '',                           // вариант оплаты (id в CRM)
    'sender'          => $sender,
    'utm_source'      => $_SESSION['utms']['utm_source'],  // utm_source
    'utm_medium'      => $_SESSION['utms']['utm_medium'],  // utm_medium
    'utm_term'        => $_SESSION['utms']['utm_term'],    // utm_term
    'utm_content'     => $_SESSION['utms']['utm_content'], // utm_content
    'utm_campaign'    => $_SESSION['utms']['utm_campaign'],// utm_campaign
    'additional_1'    => '',                               // Дополнительное поле 1
    'additional_2'    => '',                               // Дополнительное поле 2
    'additional_3'    => '',                               // Дополнительное поле 3
    'additional_4'    => ''                                // Дополнительное поле 4
);

// запрос
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://nova.lp-crm.biz/api/addNewOrder.html');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$out = curl_exec($curl);
curl_close($curl);
//$out – ответ сервера в формате JSON
?>
<?php
header("Content-Type: text/html; charset=UTF-8");

// START Telegram
$name = $_POST['name'];
$phone = $_POST['phone'];
$comment = $_POST['comment'];
$ip_r = $_SERVER['REMOTE_ADDR'];


$token = "1355939967:AAHoS1JTZ6CZYDK40u1_oDISTImOtZS2Kus";
$chat_id = "980378363";

$arr = array(
    'Товар: ' => 'Блендер (ZP-069)',
    'Имя: ' => $name,
    'Телефон: ' => $phone,
    'Время заказа: ' => date("Y-m-d H:i:s"),
    'IP адрес клиента:' =>  $ip_r,
    'Сайт: ' => $_SERVER['SERVER_NAME'],


);

foreach($arr as $key => $value) {
    $txt .= "<b>".$key."</b> ".$value."%0A";
};

$sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$txt}","r");

// End Telegram





if ($_POST['name']) {
    $name = htmlspecialchars($_POST['name']);
}
if ($_POST['phone']) {
    $phone = htmlspecialchars($_POST['phone']);
}
// if($_POST['comment']) { $comment = htmlspecialchars ($_POST['comment']); }
$ip_r = $_SERVER['REMOTE_ADDR'];

$product = "ЗАКАЗ-АП"; // Подпись отправителя

$name1 =  substr(htmlspecialchars(trim($name)), 0, 100);
$phone1 =  substr(htmlspecialchars(trim($phone)), 0, 20);

if (empty($name1)) {
    echo '<h2><p align=center><font color="red">Внимание! Запрещено отправлять пустые поля.<br> Вернитесь и заполните обязательные поля <b>"Имя"</b> и <b>"Телефон"</b></font><br><br><a href="javascript:history.back()">Вернуться назад</a></p></h2>';
    exit;
}

if (empty($phone1)) {
    echo '<h2><p align=center><font color="red">Внимание! Запрещено отправлять пустые поля.<br> Вернитесь и заполните обязательные поля <b>"Имя"</b> и <b>"Телефон"</b></font><br><br><a href="javascript:history.back()">Вернуться назад</a></p></h2>';
    exit;
}

if (isset($_POST['tip'])) {

    if ($_POST['tip'] == '1') {
        $model = 'Hover Ball 1 шт';
    }
    if ($_POST['tip'] == '2') {
        $model = 'Hover Ball 3 шт';
    }
    if ($_POST['tip'] == '3') {
        $model = 'Hover Ball 6 шт';
    }
} else {
    $model = '<span style="color:grey;">Данных нет</span>';
}

$tema_r = 'ЗАКАЗ: на Termo-6в1';
$to = ""; // ЗДЕСЬ МЕНЯЕМ ПОЧТУ НА КОТОРУЮ ПРИХОДЯТ ЗАКАЗЫ!
$from = "{$product} <noreply@{$_SERVER['HTTP_HOST']}>"; // Адрес отправителя

$subject = "=?utf-8?B?" . base64_encode("$tema_r") . "?=";
$header = "From: $from";
$header .= "\nContent-type: text/html; charset=\"utf-8\"";
$message = 'Имя: ' . $name . ' <br>Телефон: ' . $phone . ' <br><br>Заказано с лендинга: ' . $_SERVER['HTTP_REFERER'] . ' <br>IP адрес клиента: <a href="http://ipgeobase.ru/?address=' . $ip_r . '">' . $ip_r . '</a><br>Время заказа: ' . date("Y-m-d H:i:s") . '';
mail($to, $subject, $message, $header)
?>


<!doctype html>
<html lang="ua">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Дякуємо за замовлення!</title>
    <img height="1" width="1" src="https://www.facebook.com/tr?id=<?=$_COOKIE["act"]?>&ev=PageView&noscript=1"/>
    <img height="1" width="1" src="https://www.facebook.com/tr?id=<?=$_COOKIE["act"]?>&ev=Lead&noscript=1"/>
</head>
<body>
<div class="flex justify-center mt-4">
    <div class="w-full lg:w-1/2 pt-11 relative ml-8"> <!-- добавлен класс mt-20 -->
        <div class="absolute top-5 left-0">
            <img src="images/heart.png" alt="Сердечко" class="w-12 h-12 ml-12">
        </div>
        <div class="bg-green-500 rounded-lg p-6">
            <h3 class="text-2xl font-bold mb-4 text-white">Дякуємо за замовлення!</h3>
            <h5 class="text-lg text-white">Очікуйте на дзвінок менеджера<br>з 9:00 до 21:00</h5>
        </div>
    </div>
</div>


<div class="flex items-center ml-9">
    <h3 class="text-2xl font-bold mb-4 mt-8 text-center">Рекомендуємо звернути увагу:</h3>
</div>

<div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 ml-8">
    <div class="bg-white rounded-lg shadow-md">
        <a target="_blank" class="block">
            <div class="relative">
                <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 m-2 rounded-bl-lg mr-8">
                    <span>-50%</span>
                </div>
                <img class="w-full h-auto" src="images/magnit.jpg" alt="GRIP TAPE" style="">
            </div>
            <div class="p-4">
                <div class="font-bold text-xl mb-2">Магніт для ножів</div>
                <p class="text-gray-600">Потужні магніти надійно тримають ножі, навіть найбільших розмірів. Призначена
                    для кріплення до стіни та компактного зберігання ножів.</p>
                <div class="text-black-500 text-2xl inline-block line-through font-bold mr-4 mb-2 mt-2">260 грн
                </div>
                <div class="text-red-500 text-2xl font-bold inline-block">130 грн</div>
            </div>
        </a>
    </div>
</div>

<div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 ml-8">
    <div class="bg-white rounded-lg shadow-md">
        <a target="_blank" class="block">
            <div class="relative">
                <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 m-2 rounded-bl-lg mr-8">
                    <span>-50%</span>
                </div>
                <img class="w-full h-auto" src="images/drushlak.webp" alt="GRIP TAPE" style="">
            </div>
            <div class="p-4">
                <div class="font-bold text-xl mb-2">Друшляк силіконовий</div>
                <p class="text-gray-600">Складаний силіконовий друшляк круглої форми, застосовується для кухонних
                    потреб: промити зелень, овочі та фрукти</p>
                <div class="text-black-500 text-2xl inline-block line-through font-bold mr-4 mb-2 mt-2">208 грн
                </div>
                <div class="text-red-500 text-2xl font-bold inline-block">104 грн</div>
            </div>
        </a>
    </div>
</div>

<div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 ml-8">
    <div class="bg-white rounded-lg shadow-md">
        <a target="_blank" class="block">
            <div class="relative">
                <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 m-2 rounded-bl-lg mr-10">
                    <span>-50%</span>
                </div>
                <img class="w-full h-auto" src="images/vagi.webp" alt="GRIP TAPE">
            </div>
            <div class="p-4">
                <div class="font-bold text-xl mb-2">Ваги настільні кухонні</div>
                <p class="text-gray-600">Зручні кухонні ваги з платформою дозволять максимально точно виміряти кожен
                    грам продуктів завдяки сенсору високої чутливості.</p>
                <div class="text-black-500 text-2xl inline-block line-through font-bold mr-4 mb-2 mt-2">300 грн
                </div>
                <div class="text-red-500 text-2xl font-bold inline-block">150 грн</div>
            </div>
        </a>
    </div>
</div>

<div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 ml-8">
    <div class="bg-white rounded-lg shadow-md">
        <a target="_blank" class="block">
            <div class="relative">
                <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 m-2 rounded-bl-lg mr-8">
                    <span>-50%</span>
                </div>
                <img class="w-full h-auto" src="images/setka.jpg" alt="GRIP TAPE">
            </div>
            <div class="p-4">
                <div class="font-bold text-xl mb-2">Москітна сітка для вікна</div>
                <p class="text-gray-600">Сітка антимоскітна на вікна / на двері / на балкон відрізна</p>
                <div class="text-black-500 text-2xl inline-block line-through font-bold mr-4 mb-2 mt-2">250 грн
                </div>
                <div class="text-red-500 text-2xl font-bold inline-block">125 грн</div>
            </div>
        </a>
    </div>
</div>

<div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 ml-8">
    <div class="bg-white rounded-lg shadow-md">
        <a target="_blank" class="block">
            <div class="relative">
                <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 m-2 rounded-bl-lg mr-8">
                    <span>-50%</span>
                </div>
                <img class="w-full h-auto" src="images/testo.jpg" alt="GRIP TAPE">
            </div>
            <div class="p-4">
                <div class="font-bold text-xl mb-2">Універсальний тісторіз</div>
                <p class="text-gray-600">Завдяки силіконовому покриттю ви не пошкодите посуд під час збивання
                    продуктів</p>
                <div class="text-black-500 text-2xl inline-block line-through font-bold mr-4 mb-2 mt-2">180 грн
                </div>
                <div class="text-red-500 text-2xl font-bold inline-block">90 грн</div>
            </div>
        </a>
    </div>
</div>

<div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 ml-8">
    <div class="bg-white rounded-lg shadow-md">
        <a target="_blank" class="block">
            <div class="relative">
                <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 m-2 rounded-bl-lg mr-8">
                    <span>-50%</span>
                </div>
                <img class="w-full h-auto" src="images/ovochi.jpg" alt="GRIP TAPE">
            </div>
            <div class="p-4">
                <div class="font-bold text-xl mb-2">Овочечистка</div>
                <p class="text-gray-600">Овочечистка виготовлена з високоякісної нержавіючої сталі.
                    Дуже гострі леза допоможуть швидко почистити картоплю, будь-який інший овоч чи фрукт від
                    шкірки.</p>
                <div class="text-black-500 text-2xl inline-block line-through font-bold mr-4 mb-2 mt-2">174 грн
                </div>
                <div class="text-red-500 text-2xl font-bold inline-block">87 грн</div>
            </div>
        </a>
    </div>
</div>

</div>

</body>
</html>