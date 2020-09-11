<?
namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Config;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Main\Result;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PriceMaths;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\Internals\OrderPropsValueTable;

Loc::loadMessages(__FILE__);

class SibQiwiHandler extends PaySystem\ServiceHandler
{
   /**
	 * @param Payment $payment
	 * @param Request|null $request
	 * @return PaySystem\ServiceResult
	 */
	public function initiatePay(Payment $payment, Request $request = null)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/sale_payment/sibqiwi/log.txt");
		//AddMessage2Log(parent::getName());
		$errors = '';
		$phone = '';
		$data = '';
		$templateName = 'tempalte';
		$serviceResult = new PaySystem\ServiceResult();

		$paymentCollection = $payment->getCollection();
		$order = $paymentCollection->getOrder();

		if($props = $order->getPropertyCollection()){
			if($props->getItemByOrderPropertyId(23)){
				$data = $props->getItemByOrderPropertyId(23)->getValue();
				if(!empty($data) && count(explode(';', $data)) == 3){
					$explData = explode(';', $data);
					$data = [
						'orderId' => $explData[0],
						'orderTxId' => $explData[1],
						'orderSum' => $explData[2]
					];
				}
			}
			if($props->getItemByOrderPropertyId(3)){
				$phone = $props->getItemByOrderPropertyId(3)->getValue();
			}			
		}

		$orderSum = PriceMaths::roundPrecision($this->getBusinessValue($payment, 'SIBQIWI_AMOUNT'));
		if((empty($data) || (int)$data['orderSum'] != (int)$orderSum) && $phone != ''){

			if(!empty($data) && !empty($data['orderId']) && !empty($data['orderTxId'])){
				$this->clearQiwiData($payment, $data);
			}

			$data = $this->getQiwiData($payment, $phone);
			
			if($data['TYPE'] == 'OK'){
				$data = [
					'orderId' => $data['RESULT']['responseData']['orderId'],
					'orderTxId' => $data['RESULT']['responseData']['orderTxId'],
					'orderSum' => $orderSum
				];				
				if($props = $order->getPropertyCollection()){
					$dataProp = $props->getItemByOrderPropertyId(23);
					if($dataProp){
						$dataProp->setValue(implode(';', $data));
						$order->save();
					} else {
						OrderPropsValueTable::add([
							'ORDER_ID' => $order->getId(),
							'ORDER_PROPS_ID' => 23,
							'NAME' => 'QIWI Data',
							'VALUE' => serialize($data),
							'CODE' => 'SIB_QIWI_DATA'
						]);
					}
				}

			} else {
				$templateName = 'failure';
				$serviceResult->addError(new Error($data['RESULT']));
			}
		}

		if(is_array($data) && count($data) == 3){
			$shopId = $this->getBusinessValue($payment, 'SIBQIWI_SHOP_ID');
			$this->setExtraParams([
				'URL' => self::getUrlForPay($shopId, $data['orderTxId'])
			]);
		} else {
			$errors = 'Qiwi data failed';
			$templateName = 'failure';
			$serviceResult->addError(new Error($errors));
		}

		/* $template = $this->showTemplate($payment, $templateName);
		$serviceResult->setTemplate($template->getTemplate()); */

		return $serviceResult;
	}

	protected function getUrlForPay($shopId = false, $orderTxId = false)
	{
		if($orderTxId && $shopId){
			return "https://bill.qiwi.com/order/external/main.action?shop={$shopId}&transaction={$orderTxId}&successUrl=https://pay.adgroup.finance/external/tx-qiwi/success?id={$orderTxId}&failUrl=https://pay.adgroup.finance/external/tx-qiwi/fail?id={$orderTxId}&pay_source=card";
		}
		return false;
	}

	protected function clearQiwiData(Payment $payment, $data = false)
	{
		$payload = array(
			'header' => [
				'txName' => 'QiwiPayout'
			],
			'reqData' => [
				'order_id' => $data['orderId'],
				'bill_id' => $data['orderTxId'],
				'pin' => $this->getBusinessValue($payment, 'SIBQIWI_CLIENT_PIN')
			]				
		);
		return $this->post($payment, $payload, 'cancel');
	}

	protected function getQiwiData(Payment $payment, $phone)
	{
		$payload = array(
			'header' => [
				'txName' => 'QiwiPayout'
			],
			'reqData' => [
				'amount' => PriceMaths::roundPrecision($this->getBusinessValue($payment, 'SIBQIWI_AMOUNT')),
				'address' => str_replace(['+', '(', ')', '-', ' '], '', $phone),
				'platform' => 'PULL',
				'currency' => $payment->getField('CURRENCY'),
				'comment' => 'Оплата заказа ' . $this->getBusinessValue($payment, 'SIBQIWI_ORDER_NUM'),
				'pin' => $this->getBusinessValue($payment, 'SIBQIWI_CLIENT_PIN')
			]				
		);
		return $this->post($payment, $payload, 'pay');
	}

	protected function post(Payment $payment, $payload = [], $payType = 'pay')
	{
		$result = false;
		$errors = '';
				
		$http = new Web\HttpClient();
		$http->setCharset("utf-8");
		$http->setHeader("Content-Type", 'application/json', true);
		$http->setHeader("Authorization", 'Basic ' . base64_encode($this->getBusinessValue($payment, 'SIBQIWI_CLIENT_ID').':'.$this->getBusinessValue($payment, 'SIBQIWI_CLIENT_SECRET')), true);
		
		$result = $http->post($this->getUrl($payment, $payType), Web\Json::encode($payload));
		AddMessage2Log($result);
		try
		{
			$result = Web\Json::decode($result);
			if($result['errors'] !== null){
				$errors .= serialize($result['errors']);
			}
		}
		catch (ArgumentException $e)
		{
			$errors .= $e->getMessage();
		}

		if($errors === ''){
			return ['TYPE' => 'OK', 'RESULT' => $result];
		} else {
			return ['TYPE' => 'ERROR', 'RESULT' => $errors];
		}		
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getPaymentIdFromRequest(Request $request)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/sale_payment/sibqiwi/getPaymentIdFromRequest.txt");
		AddMessage2Log($request);
		return $request->get('bill_id');
    }
    
    
    /**
    * @return array
    */

    public static function getIndicativeFields()
    {
        return array('header','result', 'responseData', 'errors');
    }
    
    /**
    * @param Request $request
    * @param $paySystemId
    * @return bool
    */
    static protected function isMyResponseExtended(Request $request, $paySystemId)
    {
        return true;
    }

	/**
	 * @return mixed
	 */
    protected function getUrlList()
	{
	/* 	return array(
			'pay' => array(
				self::TEST_URL => 'http://18.185.170.86/invoice/create',
				self::ACTIVE_URL => 'https://api.adgroup.finance/invoice/create'
			),
			'cancel' => array(
				self::TEST_URL => 'http://18.185.170.86/invoice/cancel',
				self::ACTIVE_URL => 'https://api.adgroup.finance/invoice/cancel'
			)
		); */
		return array(
			'pay' => array(
				self::TEST_URL => 'http://18.185.170.86/invoice/create',
				self::ACTIVE_URL => 'https://api.adgroup.finance/invoice/create'
			),
			'cancel' => array(
				self::TEST_URL => 'http://18.185.170.86/invoice/cancel',
				self::ACTIVE_URL => 'https://api.adgroup.finance/invoice/cancel'
			)
		);
    }
    
	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return PaySystem\ServiceResult
	 */
	public function processRequest(Payment $payment, Request $request)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/sale_payment/sibqiwi/processRequest.txt");
		AddMessage2Log($payment);
		AddMessage2Log($request);

		/* $fields = array(
			"PS_STATUS" 		=> $request->get('status') == "paid" ? "Y" : "N",
			"PS_STATUS_CODE"	=> substr($request->get('status'), 0, 5),
			"PS_STATUS_MESSAGE" => Loc::getMessage("SALE_QWH_STATUS_MESSAGE_" . strtoupper($_POST['status'])),
			"PS_RESPONSE_DATE"	=> new DateTime(),
			"PS_SUM"			=> (double)$request->get('amount'),
			"PS_CURRENCY"		=> $request->get('ccy'),
			"PS_STATUS_DESCRIPTION" => ""
		); */

		return new PaySystem\ServiceResult();
	}

	/**
	 * @param Payment $payment
	 * @return bool
	 */
	protected function isTestMode(Payment $payment = null)
	{
		return $this->getBusinessValue($payment, 'SIBQIWI_PS_IS_TEST') == 'Y';
	}

	/**
	 * @return array
	 */
	public function getCurrencyList()
	{
		return array('RUB', 'USD');
	}	
}