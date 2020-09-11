<? 
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock"))
  die('exit!');
echo "<pre>";

$avitos = array(
    // новосиб
    array(
        'city_price'    => 'BASE',
        'section'       => 1493,
        'available'     => 'SIB_AVAIL_14647'
    ),
/*    array(
        'city_price'    => 1,
        'section'       => 1493,
        'available'     => 'SIB_AVAIL_14647'
    )*/

);

foreach ($avitos as $avito) {

    $city_price     = $avito['city_price'];
    $section        = $avito['section'];
    $available      = $avito['available'];

    // получаем список обрабатываемых элементов для авито
    $rsElements = CIBlockElement::GetList(array("NAME" => "ASC"), array("IBLOCK_ID" => 56, "ACTIVE"=> "Y", "SECTION_ID"=>$section, "INCLUDE_SUBSECTIONS" => "Y"), false, array("nPageSize"=>10000), array('ID', "PREVIEW_TEXT", "DETAIL_TEXT", "PROPERTY_POSTFIX", "PROPERTY_EXCEPTION"));

    while ($arElement = $rsElements->Fetch()) {

        if(in_array($arElement["ID"], $elements)) continue;
        $elements[$arElement["ID"]]=$arElement["ID"];

        echo "{$arElement["ID"]} \n==========================================\n";


        // получаем множественное свойство разделов каталога
        $VALUES = array();
        $res = CIBlockElement::GetProperty(56, $arElement["ID"], "sort", "asc", array("CODE" => "MAGAZIN_SECTIONS"));
        while ($ob = $res->GetNext())
        {
            $VALUES[] = intval($ob['VALUE']);
        }
        $arElement["PROPERTY_MAGAZIN_SECTIONS_VALUE"]=$VALUES;

        // получаем множественное свойство исключений
        $VALUES = array();
        $res = CIBlockElement::GetProperty(56, $arElement["ID"], "sort", "asc", array("CODE" => "EXCEPTION"));
        while ($ob = $res->GetNext())
        {
            $VALUES[] = $ob['VALUE'];
        }
        $arElement["PROPERTY_EXCEPTION_VALUE"]=$VALUES;

        // var_dump($arElement);

        // если список разделов НЕ пуст - прописываем в него описание

        if($arElement["PROPERTY_MAGAZIN_SECTIONS_VALUE"][0]!=0) {

	        // получаем список товаров из категорий, с наличием из москвы
	        $rsOffers = CIBlockElement::GetList(array("NAME" => "ASC"), array("IBLOCK_ID" => 6, "ACTIVE"=> "Y", "SECTION_ID" => $arElement["PROPERTY_MAGAZIN_SECTIONS_VALUE"], "INCLUDE_SUBSECTIONS"=>"Y", 'PROPERTY_'.$available.'_VALUE'=>"В наличии"), false, array("nPageSize"=>10000), array('ID', "NAME","PROPERTY_".$available, "PROPERTY_SIB_MIN_PRICE_".$city_price));
	        
	        // составляем список товаров с ценой
	        $detail_text = "";



	        while ($arOffer = $rsOffers->Fetch()) {

	            // var_dump($arOffer);

	            $final_price = $arOffer['PROPERTY_SIB_MIN_PRICE_'.$city_price . '_VALUE'];
	            //$currency_code = $arOffer['CATALOG_CURRENCY_'.$city_price];

	            // Ищем скидки и высчитываем стоимость с учетом найденных
	           /*  $arDiscounts = CCatalogDiscount::GetDiscountByProduct($arOffer['ID'], 2, "N", $city_price);
	            
	            if(is_array($arDiscounts) && sizeof($arDiscounts) > 0) {
	                $final_price = CCatalogProduct::CountPriceWithDiscount($final_price, $currency_code, $arDiscounts);
	            } */


	            print_r($arOffer['ID']);echo "<br>";
	            print_r($arOffer['NAME']);echo "<br>";
	            print_r($arOffer['PROPERTY_'.$available.'_VALUE']);echo "<br>";
	            print_r($final_price);echo "<br>";
	            echo "<br>";
	            if($arOffer['ID']){

	                foreach ($arElement["PROPERTY_EXCEPTION_VALUE"] as $exception) {
	                    $arOffer['NAME'] = str_replace($exception, "", $arOffer['NAME']);
	                }
	                
	                $detail_text .= $arOffer['NAME']." — ".number_format($final_price,0,"."," ")." ₽<br>\n";
	            }
	        }

	    }

	        // пишем детальное описание
	        $detail_text = $arElement["PREVIEW_TEXT"]."\n<br><br>".$detail_text."<br><br>\n".$arElement["PROPERTY_POSTFIX_VALUE"]["TEXT"];

	        var_dump($detail_text);

	        $el = new CIBlockElement;

	        $arLoadProductArray = Array(
	          "DETAIL_TEXT"    => $detail_text,
	          );

	        $res = $el->Update($arElement["ID"], $arLoadProductArray);

            unset($detail_text);
            
	        echo "\n\n";

    	
    }

}
?>
