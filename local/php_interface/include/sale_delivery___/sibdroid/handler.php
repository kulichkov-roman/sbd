<?
namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;

class SibdroidHandler extends Base
{
    private static $moduleId = 'sib.core';

    public static function getClassTitle()
    {
        return 'Sibdroid: Custom';
    }
        
    public static function getClassDescription()
    {
        return 'Sibdroid';
    }
        
    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
    {
        
        //$this->config["MAIN"]
        //$this->config["DEFAULTS"]

        $order = $shipment->getCollection()->getOrder(); // заказ
        //$props = $order->getPropertyCollection(); 
        //$location = $props->getDeliveryLocation()->getValue();

        $result = new CalculationResult();     
        
        //DEFAULT_PRICE_ADD
        
        $result->setPeriodDescription($this->config["DEFAULTS"]["DEFAULT_PERIOD"]);
        $currentPrice = $this->config["DEFAULTS"]["DEFAULT_PRICE"] + (($order->getPrice() - $order->getDeliveryPrice()) * ($this->config["DEFAULTS"]["DEFAULT_PRICE_ADD"] / 100));
        $ceilPrice = ceil($currentPrice / 10) * 10;
        $result->setDeliveryPrice($ceilPrice);

        $extraParams = [
            'SHEDULE' => $this->config["MAIN"]["POINT_TIME"],
            'GPS' => $this->config["MAIN"]["GPS"],
            'NAME' => $this->config["MAIN"]["POINT_NAME"],
            'ADR' => $this->config["MAIN"]["POINT_ADR"]
        ];
        $result->setDescription(json_encode($extraParams));
        
        //$result->addError(new \Bitrix\Main\Error('error'));
        
        return $result;
    }
        
    protected function getConfigStructure()
    {
        $arConfig = [
            "MAIN" => [
                "TITLE" => 'Настройка обработчика',
                "DESCRIPTION" => 'Настройка обработчика',
                "ITEMS" => array(
                    "TYPE" => array(
                        "TYPE" => "ENUM",
                        "DEFAULT" => 'self',
                        "NAME" => 'Тип доставки',
                        "OPTIONS" => [
                            'self' => 'Самовывоз',
                            'courier' => 'Курьер'
                        ]
                    ),
                    "GPS" => array(
                        "TYPE" => "STRING",
                        "DEFAULT" => '',
                        "NAME" => 'Местоположение'
                    ),
                    "POINT_NAME" => array(
                        "TYPE" => "STRING",
                        "DEFAULT" => '',
                        "NAME" => 'Название точки'
                    ),
                    "POINT_ADR" => array(
                        "TYPE" => "STRING",
                        "DEFAULT" => '',
                        "NAME" => 'Адрес точки'
                    ),
                    "POINT_TIME" => array(
                        "TYPE" => "STRING",
                        "DEFAULT" => '',
                        "NAME" => 'Режим работы'
                    ),
                )
            ],  
            "DEFAULTS" => [
                'TITLE' => 'Параметры расчета',
                'DESCRIPTION' => 'Параметры по умолчанию',
                'ITEMS' => [
                    "DEFAULT_PERIOD" => array(
                        "TYPE" => "STRING",
                        "DEFAULT" => '1 - 2 дня',
                        "NAME" => 'Сроки по умолчанию'
                    ),

                    "DEFAULT_PRICE" => array(
                        "TYPE" => "NUMBER",
                        "DEFAULT" => '1000',
                        "NAME" => 'Базовая цена'
                    ),

                    "DEFAULT_PRICE_ADD" => array(
                        "TYPE" => "NUMBER",
                        "DEFAULT" => '1000',
                        "NAME" => 'Наценка % от чека'
                    ),

                    /* "CALC_PRICE" => array(
                        "TYPE" => "Y/N",
                        "DEFAULT" => 'Y',
                        "NAME" => 'Считать объявленную стоимость от стоимость корзины'
                    ) */
                ]
            ]
        ]; 

        return $arConfig;
    }
        
    public function isCalculatePriceImmediately()
    {
        return true;
    }
        
    public static function whetherAdminExtraServicesShow()
    {
        return true;
    }
}
?>