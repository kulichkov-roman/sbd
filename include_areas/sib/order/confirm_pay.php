<?
use \Bitrix\Main\Application;
use \Bitrix\Sale\PaySystem;

//global $USER;
//if ($USER->IsAdmin() || $USER->GetId() == 55484) {
    
    $paySysBufContent = $arResult['PAY_SYSTEM']['BUFFERED_OUTPUT'];
    $needPay = true;

    /* $context = Application::getInstance()->getContext();
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
                        <ul style="display:block;width:100%;text-align:center;">
                            <?foreach($errors as $err):?>
                                <li><?=$err->getMessage()?></li>
                            <?endforeach?>
                        </ul>
                        <a class="button" style="display:block;max-width:200px;margin:10px auto;" href="/personal/order/?ORDER_ID=<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?>">Оплатить</a>
                    <?else:?>
                        <div class="thanks-box__title">Оплата заказа №<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?> прошла успешно!</div>
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
    } */

//}?>

<div class="success__box">
    <div class="thanks-box">

    <?if($needPay):?>
        <div class="thanks-box__title">Спасибо! Ваш заказ <a href="#">№<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?></a> сформирован</div>
    <?endif?>

        <?=$paySysBufContent?>
        
        <div class="thanks-box__subtitle">Менеджер свяжется с вами в ближайшее время.<br> Если у вас остались вопросы, звоните на бесплатную линию <a href="tel:88003335587">8 800 333-55-87</a></div>
        <div class="thanks-box__subtitle"><strong>Вы зарегистрированы и авторизованы.</strong><br> На указанный при оформлении заказа e-mail отправлена ссылка для смены пароля.<br> Также пароль можно сменить в <a href="/personal/" target="_blank">настройках профиля.</a></div>

    </div>
</div>