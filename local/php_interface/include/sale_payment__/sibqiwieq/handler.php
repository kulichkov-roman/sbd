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

class SibQiwiEqHandler
	extends PaySystem\ServiceHandler
{
   /**
	 * @param Payment $payment
	 * @param Request|null $request
	 * @return PaySystem\ServiceResult
	 */
	public function initiatePay(Payment $payment, Request $request = null)
	{		
		$params = [
			'FORM_ACTION' => $this->getUrl($payment, 'executeForm'),
			'FORM_REQUEST' => $this->getRequsetBaseData($payment)
		];
		$this->setExtraParams($params);
		return $this->showTemplate($payment, "template");
	}
	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getPaymentIdFromRequest(Request $request)
	{
		return $request->get('bx_payment_id');
    }    
    
    /**
    * @return array
    */

    public static function getIndicativeFields()
    {
        return array('type', 'bx_payment_id', 'ORDER_ID', 'bx_amount');
    }
    
    /**
    * @param Request $request
    * @param $paySystemId
    * @return bool
    */
    static protected function isMyResponseExtended(Request $request)
    {
        return true;
	}

	private function getCardId($userId = false)
	{
		if((int)$userId > 0){
			$userFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields('USER', $userId);
			return $userFields['UF_CARD_ID']['VALUE'];
		}
		return false;
	}

	private function checkCardIdUser(Payment $payment, Request $request)
	{
		$paymentCollection = $payment->getCollection();
		$order = $paymentCollection->getOrder();
		$uid = $order->getUserId();
		

		if((int)$uid > 0){
			$cardId = $this->getCardId($uid);
			if(empty($cardId) && $request->get('cardId')){
				$GLOBALS["USER_FIELD_MANAGER"]->Update('USER', $uid, ['UF_CARD_ID' => $request->get('cardId')]);
			}
		}
	}

	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return bool
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 * @throws \Bitrix\Main\ObjectException
	 */
	private function isCorrectSum(Payment $payment, Request $request)
	{
		$sum = $request->get('bx_amount');
		$paymentSum = $this->getBusinessValue($payment, 'SIBQIWI_AMOUNT');

		PaySystem\Logger::addDebugInfo(
			'Yandex: yandexSum='.round($sum, 2)."; paymentSum=".round($paymentSum, 2)
		);

		return round($paymentSum, 2) == round($sum, 2);
	}

	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return bool
	 * @throws \Bitrix\Main\ArgumentNullException
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 * @throws \Bitrix\Main\ObjectException
	 */
	private function isCorrectHash(Payment $payment, Request $request)
	{
		$chekFields = [
			'responseTime', 'txId', 'txTypeId', 'recurrentTypeId', 'requestId', 'orderId', 'sourceAmount', 'sourceCurrencyCode', 'amount', 'currencyCode', 'resultCode', 'resultReasonCode', 'ccNumber', 'cardId'
		];
		$hashArr = [];
		foreach($chekFields as $field){
			if(!empty($request->get($field))){
				$hashArr[] = trim($request->get($field));
			}
		}
		$key = $this->getBusinessValue($payment, 'SIBQIWIEQ_KEY') ? : 'b75c06a99be27298';
		$hash = $this->createSignature(implode('', $hashArr), $key); 

		PaySystem\Logger::addDebugInfo(
			'Sibdroid: calculatedHash='.($hash)."; requestHash=".($request->get('signature'))
		);

		return $hash === $request->get('signature');
	}
	
	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return PaySystem\ServiceResult
	 */
	public function processRequest(Payment $payment, Request $request)
	{
		$result = new PaySystem\ServiceResult();
		$action = $request->get('type');
		$statusCode = $request->get('resultCode');

		if ($this->isCorrectHash($payment, $request))
		{
			if ($action == 'return')
			{
				$fields = array(
					"PS_STATUS_CODE" => $statusCode,
					"PS_STATUS_MESSAGE" => $request->get('message'),
					"PS_SUM" => $request->get('sourceAmount'),
					"PS_CURRENCY" =>  $request->get('sourceCurrencyCode'),
					"PS_RESPONSE_DATE" => new DateTime(),
					"PS_INVOICE_ID" => $request->get('txId'),
				);

				if ($this->isCorrectSum($payment, $request))
				{
					if((int)$statusCode === 1){
						$data['CODE'] = 0;
						$fields["PS_STATUS"] = "Y";
						$result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);

						if(!empty($request->get('cardId'))){
							$this->checkCardIdUser($payment, $request);
						}

					} else {
						$errorMessage = 'Ошибка на стороне платежного модуля';
						$result->addError(new Error($errorMessage));
						PaySystem\Logger::addError($errorMessage);
					}	
					
					$result->setPsData($fields);
					$result->setData($data);
				}
				else
				{
					$errorMessage = 'Не верная сумма оплаты';
					$result->addError(new Error($errorMessage));
					PaySystem\Logger::addError($errorMessage);
				}

				$result->setData($data);
			}	
			else if ($action == 'cancel'){
				
				$errorMessage = 'Отмена оплаты на стороне платежной системы';
				$result->addError(new Error($errorMessage));
				PaySystem\Logger::addError($errorMessage);
			}		
			else
			{
				$errorMessage = 'Неизвестный ответ от сервера';
				$result->addError(new Error($errorMessage));
				PaySystem\Logger::addError($errorMessage);
			}
		}
		else
		{
			$errorMessage = 'Не верный ответ от сервера';
			$result->addError(new Error($errorMessage));
			PaySystem\Logger::addError($errorMessage);
		}

		if (!$result->isSuccess())
		{
			$errorMessage = 'Ошибка создания оплаты';
			$result->addError(new Error($errorMessage));
			PaySystem\Logger::addError($errorMessage);
		}

		return $result;
	}

	protected function getRequsetBaseData(Payment $payment)
	{		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$payId = $this->getBusinessValue($payment, 'SIBQIWIEQ_PAY_ID');
		
		$paymentCollection = $payment->getCollection();
		$order = $paymentCollection->getOrder();
		$uid = $order->getUserId();
		$cardId = $this->getCardId($uid)?:null;
		$basket = $order->getBasket();
		$itemList = [];
		foreach ($basket as $basketItem) {
			$itemList[] = [
				'itemName' => $basketItem->getField('NAME')
			];
		}

		$date = new \DateTime();

		$key = $this->getBusinessValue($payment, 'SIBQIWIEQ_KEY') ? : 'b75c06a99be27298';
		$mId = $this->hashDataInBase64($this->getBusinessValue($payment, 'SIBQIWIEQ_MID') ? : '6fb30a2c-18a2-4909-94ed-1e84df809598');
		$maId = $this->hashDataInBase64($this->getBusinessValue($payment, 'SIBQIWIEQ_MAID') ? : '5374f464-f812-42b0-b69a-48adc543df91');
		$userName = $this->encryptDataInBase64($this->getBusinessValue($payment, 'SIBQIWIEQ_USERNAME') ? : 'QPGTest257326d0baec5e3', $key);
		$pass = $this->encryptDataInBase64($this->getBusinessValue($payment, 'SIBQIWIEQ_PASSWORD') ? : '6050cfcff04c49828257326d0baec5e3', $key);
		

		$bxOrderId = $this->getBusinessValue($payment, 'SIBQIWI_ORDER_NUM');
		$orderId = $bxOrderId . '_'  . \time();
		$requestId = 'R_' . $orderId;		
		$orderDesc = 'Sibdroid.ru pay for order ' . $bxOrderId;

		$amount = PriceMaths::roundPrecision($this->getBusinessValue($payment, 'SIBQIWI_AMOUNT'));
		$currCode = 'RUB';
		$statement = 'test';

		$apiVer = '1.0.1';

		$baseUrl = 'https://sibdroid.ru/pay/confirm.php?ORDER_ID=' . $bxOrderId;
		$cancelUrl = $baseUrl.'&type=cancel&bx_payment_id=' . $payId . '&bx_amount=' . $amount;
		$returnUrl = $baseUrl.'&type=return&bx_payment_id=' . $payId . '&bx_amount=' . $amount;
		$notificationUrl = 'https://sibdroid.ru/bitrix/tools/sale_ps_result.php?ORDER_ID='.$bxOrderId.'&type=return&bx_payment_id=' . $payId . '&bx_amount=' . $amount;

		$signArr = [
			$mId,
			$maId,
			$userName,
			$pass,
			$apiVer,
			$requestId,
			$orderId,
			/* $cardId, */
			$orderDesc,
			$amount,
			$currCode,
			$statement,
			$cancelUrl,
			$returnUrl,
			$notificationUrl
		];

		$requestData = [
			'mId' => $mId,
			'maId' => $maId,
			'userName' => $userName,
			'password' => $pass, 
			'lang' => 'ru',
			'paymentMode' => 'CC',
			'signature' => $this->createSignature(implode('', $signArr), $key),
			'requestIP' => $ip,
			'metaData' => [
				'merchantUserId' => $uid
			],
			'txDetails' => [
				'apiVersion' => $apiVer,
				'requestId' => $requestId,
				'orderData' => [
					'orderId' => $orderId,
					'orderDescription' => $orderDesc,
					'amount' => $amount,
					/* 'cardId' => $cardId, */
					'currencyCode' => $currCode,
					'orderDetail' => [
						'invoiceNo' => 'No_' . $orderId,
						'ItemList' => $itemList
					]					
				],
				'cancelUrl' => $cancelUrl,
				'returnUrl' => $returnUrl,
				'notificationUrl' => $notificationUrl,
				"statement" => $statement
			]
		];
		
		return base64_encode(json_encode($requestData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	/**
	 * @return mixed
	 */
    protected function getUrlList()
	{
		$baseUrl = 'https://qpg.adgroup.finance/FE/rest/tx/';
		return array(
			'purchase' => array(
				self::TEST_URL => $baseUrl . 'sync/purchase',
				self::ACTIVE_URL => $baseUrl . 'sync/purchase'
			),
			'refund' => array(
				self::TEST_URL => $baseUrl . 'sync/refund',
				self::ACTIVE_URL => $baseUrl . 'sync/refund'
			),
			'getStatus' => array(
				self::TEST_URL => $baseUrl . 'getStatus',
				self::ACTIVE_URL => $baseUrl . 'getStatus'
			),
			'executeForm' => array(
				self::TEST_URL => $baseUrl . 'purchase/w/execute',
				self::ACTIVE_URL => $baseUrl . 'purchase/w/execute'
			),
		);
    }
    
	/**
	 * @param Payment $payment
	 * @return bool
	 */
	protected function isTestMode(Payment $payment = null)
	{
		return $this->getBusinessValue($payment, 'SIBQIWIEQ_PS_IS_TEST') == 'Y';
	}

	/**
	 * @return array
	 */
	public function getCurrencyList()
	{
		return array('RUB', 'USD');
	}

	protected function encryptDataInBase64($input, $key)
	{
		$alg = 'AES-128-ECB'; // AES-128 and ECB mode
		$ivsize = openssl_cipher_iv_length($alg);
		$iv = openssl_random_pseudo_bytes($ivsize);
		//perform encryption.
		$crypttext = openssl_encrypt($input,$alg,$key,OPENSSL_RAW_DATA,$iv);
		$encryptDataInBase64 = base64_encode($crypttext);
		return $encryptDataInBase64;
	}

	protected function hashDataInBase64($input)
	{
		$output = hash('sha256',$input,true);
		$hashDataInBase64 = base64_encode($output);
		return $hashDataInBase64;
	}

	protected function createSignature($input, $key)
	{
		$alg = 'AES-128-ECB'; // AES-128 and ECB mode $ivsize = openssl_cipher_iv_length($alg);
		$iv = openssl_random_pseudo_bytes($ivsize); //perform encryption.
		$crypttext = openssl_encrypt($input,$alg,$key,OPENSSL_RAW_DATA,$iv); //perform digest or hash on output of encryption.
		$output = hash('sha256',$crypttext,true);
		//perform base64 on output of digest/hash.
		$signature = base64_encode($output);
		return $signature ; 
	}

	//OLD
		/* public function initiatePay(Payment $payment, Request $request = null)
	{
		$bIblockModule = \Bitrix\Main\Loader::includeModule('iblock');

		if($request && $request->get('method_type') == 'cardpurchase'){
			$result = new PaySystem\ServiceResult();
			$cadrInfo = [
				'ccNumber' => $request->get('card-number'),
				'cardHolderName' => $request->get('card-holder'),
				'cvv' => $request->get('cvv'),
				'expirationMonth' => $request->get('exp-m'),
				'expirationYear' => '20' . $request->get('exp-y')
			];    
			return $result->setData($this->purchase($payment, $cadrInfo));
		}

		$orderSum = PriceMaths::roundPrecision($this->getBusinessValue($payment, 'SIBQIWI_AMOUNT'));
		$isPayed = $this->getBusinessValue($payment, 'SIBQIWIEQ_IS_PAY') === 'Y';
		$payId = $this->getBusinessValue($payment, 'SIBQIWIEQ_PAY_ID');
		$orderId = $this->getBusinessValue($payment, 'SIBQIWI_ORDER_NUM');

		$needRefund = false;
		if($bIblockModule){
			$rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 52, '=NAME' => $orderId . "_" . $payId]);
			if($ob = $rs->GetNext()){
				$data = Web\Json::decode(htmlspecialchars_decode($ob['PREVIEW_TEXT']));
				$params = [
					'TYPE' => 'DATA_SET',
					'RESULT' => $data
				];

				$needRefund = (float)$data['sourceAmount']['amount'] !== (float)$orderSum && !empty($data['txId']);
			}
		}

		if($needRefund && !$isPayed){
		}

		if(empty($data)){
			$params = ['TYPE' => 'DATA_EMPTY'];
		}

		

		$this->setExtraParams($params);

		return $this->showTemplate($payment, "template");
	} 

	public function purchase(Payment $payment, $cardInfo = [])
	{
		$requestData = $this->getRequsetBaseData($payment, $cardInfo);
		$response = $this->post($payment, $requestData, 'purchase');
		//return $response;
		$bIblockModule = \Bitrix\Main\Loader::includeModule('iblock');
		if($bIblockModule && isset($response['orderId']) && $response['redirect3DUrl']){
			$el = new \CIblockElement;
			if($el->Add([
				'IBLOCK_ID' => 52,
				'NAME' => $response['orderId'],
				'ACTIVE' => 'Y',
				'PREVIEW_TEXT' => Web\Json::encode($response)
			])){
				return ['TYPE' => 'OK', 'RESULT' => $response];
			} else {
				return ['TYPE' => 'ERROR', 'RESULT' => $el->LAST_ERROR];
			}
		}

		return ['TYPE' => 'ERROR', 'RESULT' => $response];
	}

	protected function post(Payment $payment, $payload = [], $payType = 'purchase')
	{
		$result = false;
		$errors = false;
				
		$http = new Web\HttpClient();
		$http->setCharset("utf-8");
		$http->setHeader("Content-Type", 'application/json', true);
		
		$result = $http->post($this->getUrl($payment, $payType), Web\Json::encode($payload));
		
		try
		{
			$result = Web\Json::decode($result);
		}
		catch (ArgumentException $e)
		{
			$errors = true;
		}

		return $result;
	}
*/
}