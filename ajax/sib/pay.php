<?
/* include_once "include_stop_statistic.php";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$status = ['TYPE' => 'ERROR', 'NEW_PAY_ID' => $result['payid']];

$result = array_map("htmlspecialchars", $_POST);
if(check_bitrix_sessid() && \Bitrix\Main\Loader::includeModule('sale')){

    $dbRes = \Bitrix\Sale\PaymentCollection::getList([
        'select' => ['*'],
        'filter' => [
            '=ID' => $result['payid'], 
        ]
    ]);
    
    if($pay = $dbRes->fetch()){
        $order = \Bitrix\Sale\Order::load($pay['ORDER_ID']);
        $paymentCollection = $order->getPaymentCollection();
        foreach($paymentCollection as $payment){
            if($payment->getId() == $pay['ID']){
                $sum = $payment->getSum();
                $isPaid = $payment->isPaid();

                if($isPaid){
                    continue;
                }
                
                $service = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
                $context = \Bitrix\Main\Application::getInstance()->getContext();
                $status = $service->initiatePay($payment, $context->getRequest())->getData();

                if($status['TYPE'] === 'ERROR'){
                    $payment->delete();
                    $paymentNew = $paymentCollection->createItem();
                    $paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById(27);
                    $paymentNew->setFields(array(
                        'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
                        'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
                        'SUM' => $sum,
                        'CURRENCY' => $order->getCurrency()
                    ));
                    
                    $order->save();
                    $status = [
                        'TYPE' => 'ERROR',
                        'NEW_PAY_ID' => $paymentNew->getId()
                    ];
                }
                break;
            }
        }
    }
}
echo json_encode($status);
die(); */