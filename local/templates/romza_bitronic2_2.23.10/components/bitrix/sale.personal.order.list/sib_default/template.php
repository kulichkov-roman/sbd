<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/script.js");
Asset::getInstance()->addCss("/bitrix/components/bitrix/sale.order.payment.change/templates/.default/style.css");
CJSCore::Init(array('clipboard', 'fx'));

Loc::loadMessages(__FILE__);
$sortStatus = $_REQUEST['ORDERS_STATUS'] ? : $_REQUEST['sort_status'];
if (!empty($arResult['ERRORS']['FATAL']))
{
	foreach($arResult['ERRORS']['FATAL'] as $error)
	{
		ShowError($error);
	}
	$component = $this->__component;
	if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED]))
	{
		$APPLICATION->AuthForm('', false, false, 'N', false);
	}

}
else
{
	if (!empty($arResult['ERRORS']['NONFATAL']))
	{
		foreach($arResult['ERRORS']['NONFATAL'] as $error)
		{
			ShowError($error);
		}
	}
	?>
	<?if (!count($arResult['ORDERS'])):?>
        <div class="main-orders">
            <h3 class="main-orders__title"><?= Loc::getMessage('SPOL_TPL_EMPTY_ORDER_LIST')?></h3>
            <div class="main-orders__subtitle">
                <?=str_replace("#URL#", htmlspecialcharsbx($arParams['PATH_TO_CATALOG']), Loc::getMessage('SPOL_TPL_LINK_TO_CATALOG'))?>
            </div>
        </div>
    <?else:?>
        <div class="main-orders">
            <h3 class="main-orders__title"><?=Loc::getMessage('SPOL_TPL_TITLE_ORDER_LIST')?></h3>
            <div class="main-orders__subtitle"><?=Loc::getMessage('SPOL_TPL_DESC_ORDER_LIST')?></div>
            <form>
                <div class="box-field clearfix">
                    <label class="box-field__label"><?=Loc::getMessage('SPOL_ORDER_SORT_TITLE')?>:</label>
                    <div class="box-field__input">
                        <select class="js-formstyler box-field__input-type order_status_sort">
                            <option class="option-text"><?=Loc::getMessage('SPOL_ALL_ORDERS_SORT')?></option>
                            <?foreach ($arResult['ORDERS_STATUS'] as $orderStatus):?>
                                <option class="option-text" data-status="<?=$orderStatus?>" <?if ($orderStatus === $sortStatus):?>selected<?endif?>>
                                    <?=$arResult['INFO']['STATUS'][$orderStatus]['NAME']?>
                                </option>
                            <?endforeach?>
                        </select>
                    </div>
                </div>
            </form>
            <div class="orders_list">
                <?
                    if ($_POST['ajax'] === 'Y')
                        $APPLICATION->RestartBuffer();
                ?>
                <?foreach ($arResult['ORDERS'] as $key => $order):?>
                    <? $orderHeaderStatus = $order['ORDER']['STATUS_ID'];?>
                        <ul class="main-orders__list">
                            <li class="main-orders__item clearfix">
                                <div class="order-status">
                                    <div class="order-status__top">
                                        <div class="order-status__number"><?=Loc::getMessage('SPOL_TPL_ORDER')?>
                                            <a href="#"><?=Loc::getMessage('SPOL_TPL_NUMBER_SIGN').$order['ORDER']['ACCOUNT_NUMBER']?></a>
                                        </div>
                                        <div class="order-status__date">
                                            <?=Loc::getMessage('SPOL_TPL_FROM_DATE')?>
                                            <?=$order['ORDER']['DATE_INSERT']->format($arParams['ACTIVE_DATE_FORMAT'])?>
                                        </div>
                                    </div>
                                    <div class="order-status__bottom">
                                        <div class="order-status__text"><?=Loc::getMessage('SPOL_ORDER_STATUS');?>:</div>
                                        <div class="order-status__value">
                                            <?=$arResult['INFO']['STATUS'][$orderHeaderStatus]['NAME']?>
                                            <?if ($order['ORDER']['CANCELED'] === 'Y'):?><?=Loc::getMessage('SPOL_CANCELED_ORDER');?><?endif?>
                                        </div>
                                    </div>
                                </div>
                                <div class="order-info">
                                    <ul class="order-info__list">
                                        <li class="order-info__item">
                                            <div class="order-info__text"><?=Loc::getMessage('SPOL_TPL_GOODS')?>: </div>
                                            <div class="order-info__value order-basket-items">
                                                <?foreach ($order['BASKET_ITEMS'] as $item):?>
                                                    <?=$item['NAME']?> <span>/</span>
                                                <?endforeach?>
                                            </div>
                                        </li>
                                        <li class="order-info__item">
                                            <div class="order-info__text"><?=Loc::getMessage('SPOL_TPL_SUMOF')?>:</div>
                                            <div class="order-info__value">
                                                <strong><?=number_format($order['ORDER']['PRICE'], 0, '.', ' ');?></strong>
                                                <div class="icon">
                                                    <i class="icon-rub"></i>
                                                </div>
                                            </div>
                                        </li>
                                        <?foreach ($order['PAYMENT'] as $payment):
                                        //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($payment); echo '</pre>';}
                                            $isPayBtn = $payment['PAID'] === 'N' &&  (int)$payment['PAY_SYSTEM_ID'] === 27 && $order['ORDER']['CANCELED'] === 'N';
                                            ?>
                                            <li class="order-info__item">
                                                <div class="order-info__text" style="<?=$isPayBtn ? 'padding-top:13px;' : ''?>"><?=Loc::getMessage('SPOL_TPL_PAYMENT')?>:</div>
                                                <div class="order-info__value">
                                                    <?=$payment['PAY_SYSTEM_NAME']?>                                                    
                                                    <?if($isPayBtn):?>
                                                        <span>(<?=$payment['FORMATED_SUM']?>)</span>
                                                        <div class="button" onclick="window.location.href='/pay/?ORDER_ID=<?=$order['ORDER']['ACCOUNT_NUMBER']?>'" style="margin-left:10px;margin-bottom:0;cursor:pointer;">Оплатить</div>
                                                    <?endif?>
                                                </div>
                                            </li>
                                        <?endforeach;?>
                                        <?foreach ($order['SHIPMENT'] as $shipment):?>
                                            <?if (empty($shipment)) continue;?>
                                            <li class="order-info__item">
                                                <div class="order-info__text"><?=Loc::getMessage('SPOL_TPL_SHIPMENT')?>:</div>
                                                    <div class="order-info__value"><?=$arResult['INFO']['DELIVERY'][$shipment['DELIVERY_ID']]['NAME']?></div>
                                            </li>
                                            <?if (!empty($shipment['TRACKING_NUMBER'])):?>
                                                <li class="order-info__item">
                                                    <div class="order-info__text"><?=Loc::getMessage('SPOL_TPL_POSTID')?>:</div>
                                                    <div class="order-info__value">
                                                        <strong><?=htmlspecialcharsbx($shipment['TRACKING_NUMBER'])?></strong>
                                                    </div>
                                                </li>
                                                <li class="order-info__value order-detail">
                                                    <a href="<?=htmlspecialcharsbx($shipment['LINK'])?>" target="_blank">
                                                        <?=Loc::getMessage('SPOL_TPL_MORE_ON_ORDER')?>
                                                    </a>
                                                </li>
                                            <?endif?>
                                        <?endforeach?>                                        
                                    </ul>
                                </div>
                            </li>
                        </ul>
                <?endforeach ?>
                <?=$arResult["NAV_STRING"]?>
                <?
                if ($_POST['ajax'] === 'Y')
                    return;
                ?>
            </div>
        </div>
        <?if ($_REQUEST["filter_history"] !== 'Y'):?>
            <?
            $javascriptParams = array(
                "url" => '/ajax/sib/personal_orders.php'
            );
            $javascriptParams = CUtil::PhpToJSObject($javascriptParams);
            ?>
            <script>
                BX.Sale.PersonalOrderComponent.PersonalOrderList.init(<?=$javascriptParams?>);
            </script>
        <?endif?>
	<?endif?>
<?}?>