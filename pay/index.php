<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
if(!isset($_GET['ORDER_ID'])){LocalRedirect('/');}
elseif(\Bitrix\Main\Loader::includeModule("sale"))
{
    $order = \Bitrix\Sale\Order::loadByAccountNumber($_GET['ORDER_ID']);
    if($order = \Bitrix\Sale\Order::loadByAccountNumber($_GET['ORDER_ID'])){
        if(!$order->isPaid() || $order->isCanceled()):            
            if($paymentCollection = $order->getPaymentCollection()):
                foreach($paymentCollection as $onePayment):                
                    if(!$onePayment->isPaid()):
                        $service = \Bitrix\Sale\PaySystem\Manager::getObjectById($onePayment->getPaymentSystemId());
                        $context = \Bitrix\Main\Application::getInstance()->getContext();
                    ?>
                        <section class="main-block">
                            <?$service->initiatePay($onePayment, $context->getRequest());?>
                        </section>
                    <?
                        break;
                    endif;
                endforeach;
            endif;
        else: 
            LocalRedirect('/');
        endif;
    } else {
        LocalRedirect('/');
    }    
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>