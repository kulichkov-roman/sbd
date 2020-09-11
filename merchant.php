<?php

print_r($_GET);
return;
function encryptDataInBase64($input, $key)
{
    $alg = 'AES-128-ECB'; // AES-128 and ECB mode
    $ivsize = openssl_cipher_iv_length($alg);
    $iv = openssl_random_pseudo_bytes($ivsize);
    //perform encryption.
    $crypttext = openssl_encrypt($input,$alg,$key,OPENSSL_RAW_DATA,$iv);
    $encryptDataInBase64 = base64_encode($crypttext);
    return $encryptDataInBase64;
}

function hashDataInBase64($input)
{
    $output = hash('sha256',$input,true);
    $hashDataInBase64 = base64_encode($output);
    return $hashDataInBase64;
}

function createSignature($input,$key)
{
    $alg = 'AES-128-ECB'; // AES-128 and ECB mode $ivsize = openssl_cipher_iv_length($alg);
    $iv = openssl_random_pseudo_bytes($ivsize); //perform encryption.
    $crypttext = openssl_encrypt($input,$alg,$key,OPENSSL_RAW_DATA,$iv); //perform digest or hash on output of encryption.
    $output = hash('sha256',$crypttext,true);
    //perform base64 on output of digest/hash.
    $signature = base64_encode($output);
    return $signature ; 
}

//Merchant ID : 6fb30a2c-18a2-4909-94ed-1e84df809598
//Merchant Account ID: 5374f464-f812-42b0-b69a-48adc543df91

$key = 'b75c06a99be27298';
$mId = hashDataInBase64('6fb30a2c-18a2-4909-94ed-1e84df809598');
$maId = hashDataInBase64('5374f464-f812-42b0-b69a-48adc543df91');
$userName = encryptDataInBase64('QPGTest257326d0baec5e3', $key);
$pass = encryptDataInBase64('6050cfcff04c49828257326d0baec5e3', $key);

$requestId = 'test_request_5';
$orderId = 'test_order_id_5';
$orderDesc = 'test order desc';

$amount = '100';
$currCode = 'RUB';

$apiVer = '1.0.1';

$cc = [
    'ccNumber' => '4390544362820125',
    'cardHolderName' => 'Test Testovich',
    'cvv' => '223',
    'expirationMonth' => '06',
    'expirationYear' => '2020'
];

$signArr = [
    $mId,
    $maId,
    $userName,
    $pass,
    $apiVer,
    $requestId,
    $orderId,
    $orderDesc,
    $amount,
    $currCode,
    $cc['ccNumber'],
    $cc['cardHolderName'],
    $cc['cvv'],
    $cc['expirationMonth'],
    $cc['expirationYear'],
    'test',
    'https://sibdroid.ru/merchant.php?type=cancel',
    'https://sibdroid.ru/merchant.php?type=return'
];

$requestData = [
    'mId' => $mId,
    'maId' => $maId,
    'userName' => $userName,
    'password' => $pass,
    'lang' => 'ru',
    'signature' => createSignature(implode('', $signArr), $key),
    'requestIP' => '127.0.0.1',
    'metaData' => [
        'merchantUserId' => 'very-test-user-11122'
    ],
    'txDetails' => [
        'apiVersion' => $apiVer,
        'requestId' => $requestId,
        'orderData' => [
            'orderId' => $orderId,
            'orderDescription' => $orderDesc,
            'amount' => $amount,
            'currencyCode' => $currCode,
            'cc' => $cc,
            'orderDetail' => [
                'invoiceNo' => '2019-10-10-12233-12',
                'mctMemo' => 'testMemo'
            ],
            'itemList' => [
                [
                    'itemName' => 'test item'
                ]
            ]
        ],
        'cancelUrl' => 'https://sibdroid.ru/merchant.php?type=cancel',
        'returnUrl' => 'https://sibdroid.ru/merchant.php?type=return',
        "statement" => "test"
    ]
];

header('Content-Type: application/json');
echo json_encode($requestData);