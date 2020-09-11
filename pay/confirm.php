<?
use \Bitrix\Main\Application;
use \Bitrix\Sale\PaySystem;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
if (CModule::IncludeModule("sale") && $request->get('type') != '' && $request->get('bx_payment_id') != '' && $request->get('signature') != '')
{
    $needPay = false;
    $item = PaySystem\Manager::searchByRequest($request);
    if ($item !== false)
    {
        $service = new PaySystem\Service($item);
        $payId = (int)$request->get('bx_payment_id');
        if ($service instanceof PaySystem\Service)
        {
            $result = $service->processRequest($request);
            //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($result->getErrors()[0]->getMessage()); echo '</pre>';}

            $errors = is_array($result->getErrors()) && count($result->getErrors()) > 0 ? $result->getErrors() : false;

            ob_start();
            ?>
                <?if($errors):?>
                <? //global $USER; if($USER->IsAdmin()){echo '<pre>'; var_export($_GET); echo '</pre>';} ?>
                    <div class="thanks-box__title">Ошибка оплаты:</div>
                    <br>
                    <ul style="display:block;width:100%;text-align:center;">
                        <?foreach($errors as $err):?>
                            <li><?=$err->getMessage()?></li>
                        <?endforeach?>
                    </ul>
                    <a class="button" style="display:block;max-width:200px;margin:10px auto;" href="/pay/?ORDER_ID=<?=$request->get('ORDER_ID')?>">Оплатить снова</a>
                <?else:?>
                    <div class="thanks-box__title">Оплата заказа №<?=$request->get('ORDER_ID')?> прошла успешно!</div>
                    <br>
                    <a class="button" style="display:block;max-width:250px;margin:10px auto;" href="/personal/orders/">Перейти в личный кабинет</a>
                <?endif?>
            <?
            $paySysBufContent=ob_get_clean();
                
        }
    }
    else
    {
        $debugInfo = implode("\n", $request->toArray());
        PaySystem\Logger::addDebugInfo('Pay system not found. Request: '.$debugInfo);
        ob_start();?>
            <div class="thanks-box__title">Ошибка! Не найдена платежная система.</div>
        <?
        $paySysBufContent=ob_get_clean();
    }
}

if(empty($paySysBufContent)){
    LocalRedirect('/');
}

?>
<style>
.thanks-box__title{
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    color: #222;
}
</style>
<section class="main-block">
    <?=$paySysBufContent?>
</section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>