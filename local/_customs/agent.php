<?
/*
 * Агент загрузки отзывов
 */
function AgentYandexOpinionLoad($iblock_id){
    datalog('yandexparser.log','START parse');
    CModule::IncludeModule('iblock');
    $start_time=time(); // время
    $cnt_api=0; //счетчик запросов в яндекс

    $YANDEX_OPINION_LOAD_STEP = intval(COption::GetOptionInt("catalog", "YANDEX_OPINION_LOAD_STEP"));
    datalog('yandexparser.log','start parse step='.$YANDEX_OPINION_LOAD_STEP);
    //всегда пытаемся загрузить отзывы для магазина. но загружаем лишь тогда когда количество более чем в базе
    //$YANDEX_OPINION_LOAD_STEP=1;
    //если шаг = 0 то импортируем в первую очередь настройки
    // https://tech.yandex.ru/market/content-data/doc/dg-v2/reference/shop-opinion-v2-docpage/

    if($YANDEX_OPINION_LOAD_STEP==0){
        datalog('yandexparser.log','parse step='.$YANDEX_OPINION_LOAD_STEP);
        $error_market = false;
        //забираем данные магазина
        $cnt_api++; //один запрос
        $shop_info=GetShopInfo();

        if (isset($shop_info->status) && $shop_info->status == 'ERROR'):
            datalog('yandexparser.log','Ошибка маркета='.$shop_info->errors[0]->message.', запросов='.$cnt_api.', выходим из обработки отзывов');
            $error_market = true;
        else:

            if(isset($shop_info->shop->rating->value)){
                COption::SetOptionInt("main", "shop_rating", intval($shop_info->shop->rating->value));
            }

            $countDB=COption::GetOptionInt("main", "shop_count_opinions", $shop_info->shop->rating->count);
            if(isset($shop_info->shop->rating->count)){
                COption::SetOptionInt("main", "shop_count_opinions", $shop_info->shop->rating->count);
            }

            datalog('yandexparser.log','end parse shop data');

            if($shop_info->shop->rating->count!=$countDB) {
                datalog('yandexparser.log','start parse shop opinions');
                //Предварительное удаление всех отзывов
                /*            $dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID" => IBLOCK_OPINIONS_SHOP), false, false, array('ID'));
                            while ($arRes = $dbRes->GetNext()) {
                                CIBlockElement::Delete($arRes['ID']);
                            }*/
                // datalog('yandexparser.log','delete last opinions');
                //Отзывы для магазина загружаем с яндекса
                $page = 1;
                while (true) {
                    datalog('yandexparser.log','start parse step page '.$page);
                    $opinions = GetShopOpinions($page, 30);
                    $cnt_api++;
                    //если пришла ошибка то останавливаем
                    if (isset($opinions->status) && $opinions->status == 'ERROR') {
                        datalog('yandexparser.log','BREACK ERROR');
                        break;
                    }
                    //если нет отзывов то выход
                    if ($opinions->context->page->count <= 0) {
                        datalog('yandexparser.log','BREACK 0');
                        break;
                    }
                    //перечисляем отзывы
                    foreach ($opinions->opinions as $item) {
                        $id = '';
                        $grade = '';
                        $autor = '';
                        $agree = 0;
                        $reject = 0;
                        if (isset($item->id)) {
                            $id = $item->id;
                        }
                        if (isset($item->grade)) {
                            $grade = $item->grade;
                        }
                        if (isset($item->agreeCount)) {
                            $agree = $item->agreeCount;
                        }
                        if (isset($item->disagreeCount)) {
                            $reject = $item->disagreeCount;
                        }
                        if (isset($item->author->name)) {
                            $autor = $item->author->name;;
                        }
                        $date = '';
                        if (isset($item->date)) {
                            $date = $item->date;
                        }
                        $pro = '';
                        if (isset($item->pros)) {
                            $pro = $item->pros;
                        }
                        $contra = '';
                        if (isset($item->cons)) {
                            $contra = $item->cons;
                        }
                        $text = '';
                        if (isset($item->text)) {
                            $text = $item->text;
                        }
                        $delivery = '';
                        if (isset($item->delivery->value)) {
                            $delivery = $item->delivery->value;
                        }
                        $problem = '';
                        if (isset($item->problem->value)) {
                            $delivery = $item->problem->value;
                        }
                        $authorVisibility='';
                        if(isset($item->visibility->value)){
                            $authorVisibility=$item->visibility->value;
                        }
                        $shopOrderId = '';
                        if (isset($item->shopOrderId)) {
                            $shopOrderId = $item->shopOrderId;
                        }
                        //поиск данного отзыва в базе
                        $dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID" => IBLOCK_OPINIONS_SHOP, 'PROPERTY_ID' => $id), false, false, array("ID", "NAME"));
                        if ($arRes = $dbRes->GetNext()) {
                            //отзыв найден ничего не делаем
                        } else {
                            //отзыв не найдет создаем новый
                            $el = new CIBlockElement;
                            $PROP = array();
                            $PROP['AUTOR'] = $autor;
                            $PROP['RATING'] = $grade;
                            $PROP['DATE'] = $date;
                            $PROP['PRO'] = array("VALUE" => array("TEXT" => $pro, "TYPE" => "text"));
                            $PROP['CONTRA'] = array("VALUE" => array("TEXT" => $contra, "TYPE" => "text"));
                            $PROP['TEXT'] = array("VALUE" => array("TEXT" => $text, "TYPE" => "text"));
                            $PROP['PLUS'] = $agree;
                            $PROP['MINUS'] = $reject;
                            $PROP['ID'] = $id;
                            $PROP['DELIVERY'] = $delivery;
                            $PROP['PROBLEM'] = $problem;
                            $PROP['AUTHOR_VISIBILITY'] = $authorVisibility;
                            $PROP['ORDER_OPINION_ID'] = $shopOrderId;
                            $PROP['COMMENTS'] = array("VALUE"=> array("TEXT" => json_encode($item->comments), "TYPE" => "text"));
                            $arLoadProductArray = array(
                                "IBLOCK_ID" => IBLOCK_OPINIONS_SHOP,
                                "PROPERTY_VALUES" => $PROP,
                                "NAME" => "Отзыв на магазин " . $autor . " " . $date,
                                "ACTIVE" => "Y",
                            );
                            $el->Add($arLoadProductArray);
                        }
                    }
                    $page++;
                    //Если страниц больше чем есть то останавливаем
                    if ($page > $opinions->context->page->total) {
                        datalog('yandexparser.log','BREAK TOTAL');
                        break;
                    }
                }
                datalog('yandexparser.log', 'end parse step=' . $YANDEX_OPINION_LOAD_STEP . ' cnt_api=' . $cnt_api);
                //Отзывы магазина закончились переключаем на шаг 1 - загрузка отзывов товаров.
            }

        endif;

        if($error_market==true):
            $YANDEX_OPINION_LOAD_STEP=999999; //выходим из обработки
        else:
            COption::SetOptionInt("catalog", "YANDEX_OPINION_LOAD_STEP",1);
            $YANDEX_OPINION_LOAD_STEP=1;
        endif;
    }

    //https://tech.yandex.ru/market/content-data/doc/dg-v2/reference/model-opinion-v2-docpage/
    //Загрузка отзывов на товары
    if($YANDEX_OPINION_LOAD_STEP==1){

        datalog('yandexparser.log','Загрузка отзывов на товары, шаг='.$YANDEX_OPINION_LOAD_STEP);
        //Берем товары по 100 штук за раз максимум требующие загрузку отзывов
        $dbRes = CIBlockElement::GetList(array('property_RESP_COUNT'=>'desc'), array("IBLOCK_ID"=>$iblock_id,'PROPERTY_UPDATE_OPINIONS'=>1,'!PROPERTY_'.IBLOCK_YANDEX_ID_SHOP => false,"ACTIVE"=>"Y"), false, array('nTopCount'=>500, "nPageSize"=>999999), array("ID","CODE","NAME","PROPERTY_".IBLOCK_YANDEX_ID_SHOP,"PROPERTY_RESP_COUNT","PROPERTY_RESP_QUANT", "PROPERTY_RESP_QUANT", "PROPERTY_CML2_ARTICLE"));
        while($arRes=$dbRes->GetNext()){
            //Если есть назначенный ID яндекса
            if($arRes["PROPERTY_".IBLOCK_YANDEX_ID_SHOP."_VALUE"]>0){

                datalog('yandexparser.log','НАЧИНАЕМ обрабатывать элемент='.$arRes['ID'].' '.$arRes['NAME'].' '.$arRes['PROPERTY_CML2_ARTICLE_VALUE']);
                //Назначаем параметры всего отзывов и рейтинг
                $cnt_api++;
                sleep(2);
                $product=GetProductInfo($arRes["PROPERTY_".IBLOCK_YANDEX_ID_SHOP."_VALUE"]);
                sleep(1);
                if($product != 'Thread limit'){

                
                    if (isset($product->status) && $product->status == 'ERROR'):
                        datalog('yandexparser.log','Ошибка маркета='.$product->errors[0]->message.', запросов='.$cnt_api);

                        // если такого товара нет на маркете
                        if($product->errors[0]->message == 'Model '.$arRes["PROPERTY_".IBLOCK_YANDEX_ID_SHOP."_VALUE"].' not found'):
                            // очищаем его id и прочие параметры
                            CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, "", IBLOCK_YANDEX_ID_SHOP);
                            CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, "", "RESP_COUNT");
                            CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, "", "RESP_QUANT");
                            datalog('yandexparser.log','НЕКОРРЕКТНЫЙ yandex market id у элемента='.$arRes['ID'].' '.$arRes['NAME'].' '.$arRes['PROPERTY_CML2_ARTICLE_VALUE']);
                        endif;

                        

                    endif;




                    $reviews=false;
                    if(isset($product->model->rating->count))
                        $reviews=intval($product->model->rating->count);
                    // if($reviews) var_dump($product);
                    //рейтинг существует отдельно от отзывов, поэтому записываем его в любом случае
                    $rating=false;
                    if(isset($product->model->rating->value) || !empty($product->model->rating->value)):
                        $rating=round($product->model->rating->value,2);
                    else:
                        $rating="4.5";
                    endif;

                    if($reviews!==false && $reviews!=$arRes["PROPERTY_RESP_COUNT_VALUE"]) {
                        CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, $reviews, "RESP_COUNT");
                        // echo 'set reviews<br />';
                    }
                    // if(isset($product->model->rating->distribution))
                        // CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, json_encode($product->model->rating->distribution), "YANDEKS_RATING");


                    // if($rating!==false && $rating!=$arRes["PROPERTY_RESP_QUANT_VALUE"]) {
                    CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, $rating, "RESP_QUANT");
                    //echo 'set rating<br />';
                    // }

                    datalog('yandexparser.log','Количество отзывов='.$reviews.' рейтинг='.$rating.' отзывов в базе='.$arRes["PROPERTY_RESP_COUNT_VALUE"].' рейтинг в базе='.$arRes["PROPERTY_RESP_QUANT_VALUE"]);
                    //если количество отзывов равно тому сколько загружено то пропускаем товар
                    if($reviews>0){
                        //Загружаем отзывы
                        $page=1;
                        //while(true){
                        $cnt_api++;

                        $openingsRes=GetProductOpinions($arRes["PROPERTY_".IBLOCK_YANDEX_ID_SHOP."_VALUE"],$page,30);
                        if (isset($openingsRes->status) && $openingsRes->status == 'ERROR'):
                            datalog('yandexparser.log','Ошибка маркета='.$openingsRes->errors[0]->message.', запросов='.$cnt_api);
                            break;
                        endif;

                        sleep(3);

                        //останавливаем если отзывов нет
                        // if(count($openingsRes->opinions)==0):
                        //     datalog('yandexparser.log','Отзывов нет, идем дальше');
                        //     break;
                        // endif;

                        $otziv=0;
                        //отзывы могут обновиться и у них изменится id, так что предварительно удалим все существующие отзывы
                        $rsElementsO = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>IBLOCK_OPINIONS_PRODUCT,'PROPERTY_PRODUCT_ID'=>$arRes["ID"]), false, array('nTopCount'=>100000), array("ID"));
                        while($obElementO = $rsElementsO->GetNext()){
                            datalog('yandexparser.log','Отзыв есть, очищаем: '.$obElementO['ID']);
                            CIBlockElement::Delete($obElementO['ID']);
                            $otziv++;
                        }

                        datalog('yandexparser.log','Отзывов удалено='.$otziv);

                        //пробегаем по пришедшим отзывам
                        foreach($openingsRes->opinions as $item){
                            //создание нового отзыва
                            $id='';
                            $grade='';
                            $autor='';
                            if(isset($item->id)){
                                $id=$item->id;
                            }
                            if(isset($item->grade)){
                                $grade=$item->grade;
                            }
                            if(isset($item->author->name)){
                                $autor=$item->author->name;
                            }
                            $date='';
                            if(isset($item->date)){
                                $date=$item->date;
                            }
                            $pro='';
                            if(isset($item->pros)){
                                $pro=$item->pros;
                                $pro = preg_replace('~<a(.*?)href="([^"]+)"(.*?)>*<\/a>~', "", $pro);
                            }
                            $contra='';
                            if(isset($item->cons)){
                                $contra=$item->cons;
                                $contra = preg_replace('~<a(.*?)href="([^"]+)"(.*?)>*<\/a>~', "", $contra);
                            }
                            $text='';
                            if(isset($item->text)){
                                $text=$item->text;
                                $text = preg_replace('~<a(.*?)href="([^"]+)"(.*?)>*<\/a>~', "", $text);
                            }
                            $agreeCount=0;
                            if(isset($item->agreeCount)){
                                $agreeCount=$item->agreeCount;
                            }
                            $disagreeCount=0;
                            if(isset($item->disagreeCount)){
                                $disagreeCount=$item->disagreeCount;
                            }
                            $usageTime='';
                            if(isset($item->usageTime->value)){
                                $usageTime=$item->usageTime->value;
                            }
                            $verifiedBuyer='';
                            if(isset($item->verifiedBuyer->value)){
                                $verifiedBuyer=$item->verifiedBuyer->value;
                            }
                            $authorVisibility='';
                            if(isset($item->visibility->value)){
                                $authorVisibility=$item->visibility->value;
                            }
                            //Поиск в базе данного отзыва
                            // $rsElementsO = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>IBLOCK_OPINIONS_PRODUCT,'PROPERTY_ID'=>$id), false, false, array("ID","NAME"));
                            // if($obElementO = $rsElementsO->GetNext()){

                            // }else{
                            datalog('yandexparser.log',"Создаем новый отзыв на товар ".$arRes["NAME"].' '.$id);

                            $el = new CIBlockElement;
                            $PROP = array();
                            $PROP['AUTOR'] = $autor;
                            $PROP['RATING'] = $grade;
                            $PROP['DATE'] = $date;
                            $PROP['PRO'] = Array("VALUE" => Array ("TEXT" => $pro, "TYPE" => "text"));
                            $PROP['CONTRA'] = Array("VALUE" => Array ("TEXT" => $contra, "TYPE" => "text"));
                            $PROP['TEXT'] = Array("VALUE" => Array ("TEXT" => $text, "TYPE" => "text"));
                            $PROP['PRODUCT_ID'] = $arRes["ID"];
                            $PROP['MARKET_ID'] = $arRes["PROPERTY_".IBLOCK_YANDEX_ID_SHOP."_VALUE"];
                            $PROP['ID'] = $id;
                            $PROP['AGREE_COUNT'] = $agreeCount;
                            $PROP['DISAGREE_COUNT'] = $disagreeCount;
                            $PROP['USAGE_TIME'] = $usageTime;
                            $PROP['VERIFIED_BUYER'] = $verifiedBuyer;
                            $PROP['AUTHOR_VISIBILITY'] = $authorVisibility;
                            $arLoadProductArray = Array(
                                "IBLOCK_ID"      => IBLOCK_OPINIONS_PRODUCT,
                                "PROPERTY_VALUES"=> $PROP,
                                "NAME"           => "Отзыв на товар ".$arRes["NAME"].' '.$id,
                                "ACTIVE"         => "Y",
                            );
                            if(strlen($pro)>0 || strlen($contra)>0 || strlen($text)>0 || strlen($date)>0){
                                $el->Add($arLoadProductArray);
                            }
                            // }
                        }

                        //если количество запросов более 90 то останавливаем
                        //if($cnt_api>=90) break;
                        //Если страниц больше чем есть то останавливаем
                        //if ($page > $openingsRes->context->page->total) {
                        //    break;
                        //}
                        //}
                    }else{
                            datalog('yandexparser.log','ПРОПУСКАЕМ элемент='.$arRes['ID'].' '.$arRes['NAME'].' '.$arRes['PROPERTY_CML2_ARTICLE_VALUE']);
                    }

                    datalog('yandexparser.log','КОНЕЦ обработки элемент='.$arRes['ID'].' '.$arRes['NAME'].' '.$arRes['PROPERTY_CML2_ARTICLE_VALUE']);
                    
                } else {
                    datalog('yandexparser.log','Thread limit '.$arRes['ID'].' '.$arRes['NAME'].' '.$arRes['PROPERTY_CML2_ARTICLE_VALUE']);
                }

            }else{
                //Так как нет назначения яндекс ID то сбросим на ноль количество отзывов и рейтинг
                CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, 0, "RESP_COUNT");
                CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, 0, "RESP_QUANT");
            }

            datalog('yandexparser.log','УБИРАЕМ МЕТКУ элемент='.$arRes['ID'].' '.$arRes['NAME'].' '.$arRes['PROPERTY_CML2_ARTICLE_VALUE']);
            //Уберем метку того что надо парсить данный товар
            CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, 0, "UPDATE_OPINIONS");
            //Сброс если количество запросов превысило 90
            if($cnt_api>=10000):
                datalog('yandexparser.log','Превышено количество запросов='.$cnt_api.', завершаемся');
                break;
            endif;
        }
        datalog('yandexparser.log','КОНЕЦ обработки шаг='.$YANDEX_OPINION_LOAD_STEP.' запросов='.$cnt_api);

        //Считаем количество осавшихся не парсенных товаров
        $cnt = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$iblock_id,'PROPERTY_UPDATE_OPINIONS'=>1,'!PROPERTY_'.IBLOCK_YANDEX_ID_SHOP => false,"ACTIVE"=>"Y"), array());
        //Если количество ==0 то сбрасываем парсер в первоначальное состояние
        if($cnt==0){

            //Если количество товаров для индексации равно нулю то заного запускаем индексацию. Устанавливаем всем товарам требующим интексацию флаг индексации
            $indexsesProductCNT=CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$iblock_id,'PROPERTY_UPDATE_OPINIONS'=>1, '!PROPERTY_'.IBLOCK_YANDEX_ID_SHOP => false, "ACTIVE"=>"Y"),array());
            if($indexsesProductCNT==0) {
                $dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $iblock_id, '!PROPERTY_'.IBLOCK_YANDEX_ID_SHOP => false,"ACTIVE"=>"Y"), false, false, array("ID", "CODE", "NAME", "PROPERTY_".IBLOCK_YANDEX_ID_SHOP));
                while ($arRes = $dbRes->GetNext()) {
                    CIBlockElement::SetPropertyValues($arRes["ID"], $iblock_id, 1, "UPDATE_OPINIONS");
                }
            }

            COption::SetOptionInt("catalog", "YANDEX_OPINION_LOAD_STEP",0);
            datalog('yandexparser.log','Товаров не осталось, парсим заново, шаг=1');
        }else{
            datalog('yandexparser.log','Следующее количество для обработки='.$cnt);
        }
    }

    datalog('yandexparser.log','КОНЕЦ обработки');
    return "AgentYandexOpinionLoad(".$iblock_id.");";
}

function AvitoPictures() {

    CModule::IncludeModule('iblock');
    CModule::IncludeModule('yenisite.resizer2');

    $rsElements = CIBlockElement::GetList(array("NAME" => "ASC"), array("IBLOCK_ID" => 6));

    $rsElement = new CIBlockElement;

    $iHeight = 468;
    $iWidth = 624;
    CResizer2Resize::ClearCacheByID(37);
    while ($arElement = $rsElements->Fetch()) {

      if ($arElement["DETAIL_PICTURE"] != "") {
        $arPreview = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);

        // $arPreview = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array('width' => $iWidth, 'height' => $iHeight), BX_RESIZE_IMAGE_PROPORTIONAL, false);

        $resized = CResizer2Resize::ResizeGD2($arPreview["SRC"], 37);
        // $resized = 'https://sibdroid.ru'.$resized;
        // var_dump($resized);
        // exit();
        // $arLoadProductArray = Array(
        //   // "DETAIL_PICTURE"  => CFile::MakeFileArray(CFile::GetPath($arElement["DETAIL_PICTURE"])),
        //   "PREVIEW_PICTURE" => CFile::MakeFileArray($arPreview["src"]),
        // );   

        // if ($rsElement->Update($arElement["ID"], $arLoadProductArray)) {
        $rsElement->SetPropertyValues($arElement["ID"], 6, $resized, 'AVITO_PICTURE');
        // echo "Обновилась картинка у товара {$arElement["ID"]}<br />\n";

      }
    }

    return "AvitoPictures();";

}

if(!function_exists('diffHours')){
    function diffHours(DateTime $datetime1, DateTime $datetime2 = null)
    {
        if (!isset($datetime2)) {
            $datetime2 = new DateTime('now');
        }    
        $interval = $datetime1->diff($datetime2, false);    
        return floor(($datetime2->getTimestamp() - $datetime1->getTimestamp()) / (60 * 60));
    }
}
//агент проставления сортировки разделов в зависимости от просмотров

if (!function_exists('checkSortSectionsDepthFour')) {
    function getShowCount()
    {
        CModule::IncludeModule('iblock');

        $arShowCount = [];
        /* $arFilterStore = [
            'LOGIC' => 'OR',
            ['>CATALOG_STORE_AMOUNT_1' => '0'],
            ['>CATALOG_STORE_AMOUNT_2' => '0'],
            ['>CATALOG_STORE_AMOUNT_15' => '0'],
            ['>CATALOG_STORE_AMOUNT_16' => '0']
        ]; */

        $rsItems = \CIBlockElement::GetList(['IBLOCK_SECTION_ID' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'/* , $arFilterStore */], false, false, ['ID', 'SHOW_COUNTER', 'SHOW_COUNTER_START', 'IBLOCK_SECTION_ID', 'CATALOG_AVAILABLE']);
        while ($item = $rsItems->GetNext()) {
            if ((int)$item["IBLOCK_SECTION_ID"] > 0) {
                $diffHours = diffHours(new DateTime($item['SHOW_COUNTER_START']));
                if($item["CATALOG_AVAILABLE"] == 'Y'){
                    $arShowCount[$item["IBLOCK_SECTION_ID"]][] = round(($item["SHOW_COUNTER"] / ($diffHours + 1)) * 100);
                } else {
                    $arShowCount[$item["IBLOCK_SECTION_ID"]][] = 0;
                }
                
            }
        }

        return $arShowCount;
    }
}

if (!function_exists('checkSortSectionsDepthFour')) {
    function checkSortSectionsDepthFour()
    {
        CModule::IncludeModule('iblock');
        $arShowCount = getShowCount();

        
        $rsSections = \CIBlockSection::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', '=DEPTH_LEVEL' => 4], false, ['ID', 'IBLOCK_SECTION_ID', 'SORT', 'DEPTH_LEVEL']);
        $arSectionSorting = [];
        while ($section = $rsSections->GetNext()) {
            $arSectionSorting[$section['ID']] += $section['SORT'];
        }

        $arAvgCount = [];
        foreach ($arShowCount as $key => $elements) {
            $sumElements = 0;
            $cntElements = 0;
            foreach ($elements as $val) {
                if (is_array($val)) {
                    foreach ($val as $v) {
                        $sumElements += $v;
                        $cntElements++;
                    }
                } else {
                    $sumElements += $val;
                    $cntElements++;
                }
            }
            $arAvgCount[$key] = round($sumElements / $cntElements);
        }
        $arShowCount = $arAvgCount;

        $upSection = new \CIBlockSection;
        foreach ($arSectionSorting as $id => $val) {
            if (isset($arShowCount[$id]) && $val != $arShowCount[$id]) {
                $upSection->Update($id, array('SORT' => $arShowCount[$id]));
            }
        }

        return "checkSortSectionsDepthFour();";
    }
}

if (!function_exists('checkSortSections')) {
    function checkSortSections($step, $stepSize)
    {
        CModule::IncludeModule('iblock');
    
        $arShowCount = getShowCount();

        /* $arFilterStore = [
            'LOGIC' => 'OR',
            ['>CATALOG_STORE_AMOUNT_1' => '0'],
            ['>CATALOG_STORE_AMOUNT_2' => '0'],
            ['>CATALOG_STORE_AMOUNT_15' => '0'],
            ['>CATALOG_STORE_AMOUNT_16' => '0']
        ];
        $rsItems = \CIBlockElement::GetList(['IBLOCK_SECTION_ID' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', $arFilterStore], false, false, ['ID', 'SHOW_COUNTER', 'SHOW_COUNTER_START', 'IBLOCK_SECTION_ID']);
        while ($item = $rsItems->GetNext()) {
            if ((int)$item["IBLOCK_SECTION_ID"] > 0) {
                $diffHours = diffHours(new DateTime($item['SHOW_COUNTER_START']));
                $arShowCount[$item["IBLOCK_SECTION_ID"]][] = round(($item["SHOW_COUNTER"] / ($diffHours + 1)) * 100);
            }
        } */
            
        $arSectionSorting = [];
        $arParents = [];
        $rsSections = \CIBlockSection::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', '>=DEPTH_LEVEL' => 3], false, ['ID', 'IBLOCK_SECTION_ID', 'SORT', 'DEPTH_LEVEL']);
        while ($section = $rsSections->GetNext()) {
            if ($section['DEPTH_LEVEL'] == 4) {
                $arSectionSorting[$section['IBLOCK_SECTION_ID']] += $section['SORT'];
                $arParents[$section['ID']] = $section['IBLOCK_SECTION_ID'];
            } else {
                $arSectionSorting[$section['ID']] += $section['SORT'];
            }
        }

        foreach ($arParents as $id => $parentId) {
            if (isset($arShowCount[$id])) {
                $sum = 0;
                foreach ($arShowCount[$id] as $v) {
                    $sum += $v;
                }
                $sum = round($sum / count($arShowCount[$id]));
                if (!isset($arShowCount[$parentId]) || $arShowCount[$parentId][0] < $sum) {
                    $arShowCount[$parentId] = [$sum];
                }
                unset($arShowCount[$id]);
            }
        }

        $arAvgCount = [];
        foreach ($arShowCount as $key => $elements) {
            $sumElements = 0;
            $cntElements = 0;
            foreach ($elements as $val) {
                if (is_array($val)) {
                    foreach ($val as $v) {
                        $sumElements += $v;
                        $cntElements++;
                    }
                } else {
                    $sumElements += $val;
                    $cntElements++;
                }
            }
            $arAvgCount[$key] = round($sumElements / $cntElements);
        }
        $arShowCount = $arAvgCount;
    

        if ($stepSize > 0) {
            $tmpSectionSorting = array_chunk($arSectionSorting, $stepSize, true);
            if (isset($tmpSectionSorting[$step])) {
                $arSectionSorting = $tmpSectionSorting[$step];
            }
        }
            
        $upSection = new \CIBlockSection;
        foreach ($arSectionSorting as $id => $val) {
            if (isset($arShowCount[$id]) && $val != $arShowCount[$id]) {
                $upSection->Update($id, array('SORT' => $arShowCount[$id]));
            }
        }

        $step = $step < (count($tmpSectionSorting) - 1) ? $step + 1 : 0;
    
        return "checkSortSections({$step}, {$stepSize});";
    }
}

if (!function_exists('checkDisabledSections')) {
    function checkDisabledSections()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $rs  = \CIblockElement::GetList(['CATALOG_AVAILABLE' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'], false, false, ['PROPERTY_RBS_STORE_DATE_INFO', 'ID','IBLOCK_SECTION_ID']);
            $arSectionsInfo = [];
            /* while ($ob = $rs->GetNext()) {
                if ((int)$ob['IBLOCK_SECTION_ID'] <= 0) {
                    continue;
                }
                if ($ob['CATALOG_AVAILABLE'] == 'Y') {
                    $arSectionsInfo[$ob['IBLOCK_SECTION_ID']] = false;
                    continue;
                }
                $arAvailableDateInfo = unserialize(htmlspecialchars_decode($ob['PROPERTY_RBS_STORE_DATE_INFO_VALUE']));
                $maxDays = false;
                foreach ($arAvailableDateInfo as $storeId => $info) {
                    $dateTimeLast = new \DateTime($info['DATE']);
                    $dateTimeCurrent = new \DateTime(date('Y-m-d'));
                    if (!$maxDays) {
                        $maxDays = $dateTimeCurrent->diff($dateTimeLast)->days > 30;
                    }
                }
                $arSectionsInfo[$ob['IBLOCK_SECTION_ID']] = $maxDays;
            } */
            while ($ob = $rs->GetNext()) {
                if ((int)$ob['IBLOCK_SECTION_ID'] <= 0) {
                    continue;
                }

                //Добавлено: Если до этого уже был товар в наличии - не проверяем
                if ((isset($arSectionsInfo[$ob['IBLOCK_SECTION_ID']])) && (!$arSectionsInfo[$ob['IBLOCK_SECTION_ID']])){
                    continue;
                }

                if ($ob['CATALOG_AVAILABLE'] == 'Y') {
                    $arSectionsInfo[$ob['IBLOCK_SECTION_ID']] = false;
                    continue;
                }
                $arAvailableDateInfo = unserialize(htmlspecialchars_decode($ob['PROPERTY_RBS_STORE_DATE_INFO_VALUE']));
               
                //Переписано: поиск минимального кол-ва прошедших дней, если их меньше 30 - false
                $minDays = 31;
                foreach ($arAvailableDateInfo as $storeId => $info) {
                    $dateTimeLast = new \DateTime($info['DATE']);
                    $dateTimeCurrent = new \DateTime(date('Y-m-d'));
                    $daysLat = $dateTimeCurrent->diff($dateTimeLast)->days;
                    if ($minDays > $daysLat){
                        $minDays = $daysLat;
                    }
                }

                if ($minDays < 30){
                    $minDays = false;
                } else {
                    $minDays = true;
                }
                
                $arSectionsInfo[$ob['IBLOCK_SECTION_ID']] = $minDays;
                
            }
        
            $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', 'ID' => array_keys($arSectionsInfo)], false, ['ID', 'UF_*']);
            while ($ob = $rs->GetNext()) {
                $isHidden = $ob['UF_IS_SHOW'] && $ob['UF_DIS_CATALOG'];
                $val = -1;

                if (!$arSectionsInfo[$ob['ID']] && $isHidden) {
                    $val = 0;
                } elseif ($arSectionsInfo[$ob['ID']] && !$isHidden) {
                    $val = 1;
                }
                if ($val > -1) {
                    $GLOBALS["USER_FIELD_MANAGER"]->Update('IBLOCK_6_SECTION', $ob['ID'], ['UF_IS_SHOW' => $val,'UF_DIS_CATALOG' => $val]);
                }
            }
        }
        return "checkDisabledSections();";
    }
}

if (!function_exists('checkDisabledSectionsParents')) {
    function checkDisabledSectionsParents()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $rs = \CIblockSection::GetList(['DEPTH_LEVEL' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', '>=DEPTH_LEVEL' => 2], false, ['ID', 'UF_*', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL']);
            $arAllSections = [];
            $arHiddenSections = [];
            while ($ob = $rs->GetNext()) {
                $isHidden = $ob['UF_IS_SHOW'] && $ob['UF_DIS_CATALOG'];
                $arHiddenSections[$ob['ID']] = $isHidden;
                $arAllSections[$ob['IBLOCK_SECTION_ID']][$ob['ID']] = $isHidden;
            }
        
            foreach ($arAllSections as $sectionId => $sections) {
        
                sort($sections);
        
                $isHiddenSection = $arHiddenSections[$sectionId];
                $isNeedHidden = $sections[0];
        
                $val = -1;
                if ($isNeedHidden && !$isHiddenSection) {
                    $val = 1;
                    //print_r([$sectionId => $sections]);
                }
                if (!$isNeedHidden && $isHiddenSection) {
                    $val = 0;
                    //print_r([$sectionId => $sections]);
                }
        
                if ($val > -1) {
                    $GLOBALS["USER_FIELD_MANAGER"]->Update('IBLOCK_6_SECTION', $sectionId, ['UF_IS_SHOW' => $val,'UF_DIS_CATALOG' => $val]);
                }
            }
        }

        return "checkDisabledSectionsParents();";
    }
}

if (!function_exists('checkRecomend')) {
    function checkRecomend()
    {
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("sale");

        $arDisableSections = [56, 608];
        $arDisableSubSections = [];
        $arOnlyAccessSections = [];
        foreach ($arDisableSections as $sectionId) {
            $rsParentSection = CIBlockSection::GetByID($sectionId);
            if ($arParentSection = $rsParentSection->GetNext()) {
                $arFilter = [
                    'IBLOCK_ID' => $arParentSection['IBLOCK_ID'],
                    '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],
                    '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],
                    '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']
                ];
                $rsSect = CIBlockSection::GetList(['left_margin' => 'asc'], $arFilter);
                while ($arSect = $rsSect->GetNext()) {
                    $arDisableSubSections[] = $arSect['ID'];
                    if ($sectionId == 56) {
                        $arOnlyAccessSections[] = $arSect['ID'];
                    }
                }
            }
        }
        $arDisableSections = array_merge($arDisableSections, $arDisableSubSections);

        $arSelect = ['ID','IBLOCK_ID','PROPERTY_REKOMENDUEYE_TOVARY','IBLOCK_SECTION_ID'];
        $arFilterItems = [
            'IBLOCK_ID' => 6,
            'ACTIVE' => 'Y',
            [
                "LOGIC" => "AND",
                ['!PROPERTY_REKOMENDUEYE_TOVARY' => false],
                ['!PROPERTY_REKOMENDUEYE_TOVARY' => 1123]
            ],
            '!IBLOCK_SECTION_ID' => $arDisableSections
        ];
        $arFilterRecommend = [
            'IBLOCK_ID'=> 6,
            'IBLOCK_SECTION_ID' => $arOnlyAccessSections,
            '>CATALOG_QUANTITY' => 0
        ];
        $dbRes = CIBlockElement::GetList([], $arFilterItems, false, false, $arSelect);
        while ($arRes = $dbRes->GetNext()) {
            $arRecomentID = [];
            $arFilterRecommend['PROPERTY_REKOMENDUEYE_TOVARY'] = $arRes['PROPERTY_REKOMENDUEYE_TOVARY_ENUM_ID'];
            $dbRes2 = CIBlockElement::GetList([], $arFilterRecommend, false, false, ['ID']);
            while ($arRes2 = $dbRes2->GetNext()) {
                $arRecomentID[] = $arRes2['ID'];
            }
            CIBlockElement::SetPropertyValues($arRes['ID'], $arRes['IBLOCK_ID'], $arRecomentID, "RECOMMEND");
        }
        return 'checkRecomend();';
    }
}
if (!function_exists('checkItemsForTime')) {
    function checkItemsForTime()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $catalogIblockId = 6;
            $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => $catalogIblockId, '!UF_INTERVAL_YA' => false], false, ['ID', 'UF_INTERVAL_YA']);
            while ($ob = $rs->GetNext()) {
                //AddMessage2Log($ob);
                $ob['UF_INTERVAL_YA'] = trim($ob['UF_INTERVAL_YA']);
                if (!empty($ob['UF_INTERVAL_YA'])) {
                    $expl = explode('-', $ob['UF_INTERVAL_YA']);
                    if (count($expl) == 2) {
                        $startTime = strtotime(date('Y-m-d ') . trim($expl[0]));
                        $endTime = strtotime(date('Y-m-d ') . trim($expl[1]));
                        $currentTime = strtotime(date('Y-m-d H:i'));
    
                        if ($currentTime >= $startTime && $currentTime <= $endTime) {
                            $els = \CIblockElement::GetList([], ['IBLOCK_ID' => $catalogIblockId, 'SECTION_ID' => $ob['ID'], 'INCLUDE_SUBSECTIONS' => 'Y'], false, false, ['ID']);
                            while ($obEl = $els->GetNext()) {
                                \CIBlockElement::SetPropertyValuesEx($obEl['ID'], $catalogIblockId, ['RBS_INTERVAL_YA' => 2645]); //Y
                            }
                        } else {
                            $els = \CIblockElement::GetList([], ['IBLOCK_ID' => $catalogIblockId, 'SECTION_ID' => $ob['ID'], 'INCLUDE_SUBSECTIONS' => 'Y', 'PROPERTY_RBS_INTERVAL_YA_VALUE' => 'Y'], false, false, ['ID']);
                            while ($obEl = $els->GetNext()) {
                                \CIBlockElement::SetPropertyValuesEx($obEl['ID'], $catalogIblockId, ['RBS_INTERVAL_YA' => false]);
                            }
                        }
                    }
                }
            }
        }

        return 'checkItemsForTime();';
    }
}

if (!function_exists('getYaCampaigns')) {
    function getYaCampaigns(){
        if(\Bitrix\Main\Loader::includeModule('iblock')){

            $clientId = 'a36d7127ed184197946c5b52bcaba5fd';
            $token = 'AgAAAAAOxNBgAAYV1U8ivq-_m0K_ncjH7WkRNLk';
        
            $iblockId = 55;
            $el = new \CIblockElement;
        
            $httpClient = new \Bitrix\Main\Web\HttpClient();
            $httpClient->setHeader('Content-Type', 'application/json', true);
            $httpClient->setHeader('Authorization', 'OAuth oauth_token="'.$token.'", oauth_client_id="'.$clientId.'"', true);
            
            $pageNumber = 0;
            
            do{
                $pageNumber++;
            
                $response = $httpClient->get('https://api.partner.market.yandex.ru/v2/campaigns.json?' . http_build_query(array('page' => $pageNumber)));
                $campaignResult = json_decode($response);
                if(is_object($campaignResult)){
                    foreach($campaignResult->campaigns as $campaign){
                        $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => $iblockId, 'XML_ID' => $campaign->id]);
                        if(!$rs->GetNext()){
                            $el->Add([
                                'IBLOCK_ID' => $iblockId,
                                'NAME' => $campaign->domain,
                                'XML_ID' => $campaign->id
                            ]);
                        }
                    }
                }
            
            
                $campaignsTotalPages = $campaignResult->pager->pagesCount;
            } while ($pageNumber != $campaignsTotalPages);
        }

        return 'getYaCampaigns();';
    }
}

if (!function_exists('getYaReviewApi')) {
    function getYaReviewApi($campaignId = '', $pageToken = '')
    {
        if(\Bitrix\Main\Loader::includeModule('iblock')){
            $rsCamp = \CIblockElement::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => 55, 'ACTIVE' => 'Y'], false, false, ['ID','NAME','XML_ID']);
            $arCamp = [];
            while($obCamp = $rsCamp->fetch()){
                $arCamp[] = ['ID' => $obCamp['XML_ID'], 'NAME' => $obCamp['NAME']];
            }

            $campaign = [];
            if($campaignId == 0){
                $campaign = array_shift($arCamp);
            } else {
                foreach($arCamp as $k => $v){
                    if($v['ID'] == $campaignId){
                        $campaign = $v;
                        unset($arCamp[$k]);
                        break;
                    }
                    unset($arCamp[$k]);
                }
            }

            if($campaign['ID'] > 0){
                $clientId = 'a36d7127ed184197946c5b52bcaba5fd';
                $token = 'AgAAAAAOxNBgAAYV1U8ivq-_m0K_ncjH7WkRNLk';
            
                $iblockId = 54;
                $el = new \CIblockElement;
            
                $httpClient = new \Bitrix\Main\Web\HttpClient();
                $httpClient->setHeader('Content-Type', 'application/json', true);
                $httpClient->setHeader('Authorization', 'OAuth oauth_token="'.$token.'", oauth_client_id="'.$clientId.'"', true);
                
                $arRequestParams = ['limit' => 100, 'from_date' => date('Y-m-d')];
                if(!empty($pageToken)){
                    $arRequestParams['page_token'] = $pageToken;
                }
                
                $response = $httpClient->get('https://api.partner.market.yandex.ru/v2/campaigns/'.$campaign['ID'].'/feedback/updates.json?'. http_build_query($arRequestParams));
                $feedbackResult = json_decode($response);
                
                if(is_object($feedbackResult)){
                    if($feedbackResult->status === 'OK'){
                        $feedbackResult = $feedbackResult->result;
                        
                        $nextPageToken = $feedbackResult->paging->nextPageToken;
                        $feedbackList = $feedbackResult->feedbackList;

                        if(is_array($feedbackList) && count($feedbackList) < 100){
                            $nextPageToken = '';
                        }

                        foreach($feedbackList as $feedback){
                            $thisFeedBack = [
                                'ID' => $feedback->id,
                                'DATE' => $feedback->createdAt,
                                'RECOMMEND' => $feedback->recommend,
                                'VERIFY' => $feedback->verified,
                                'PRO' => $feedback->pro,
                                'CONTRA' => $feedback->contra,
                                'TEXT' => $feedback->text,

                                'DOMAIN' => $campaign['NAME'],
                                'CAMP_ID' => $campaign['ID']
                            ];
                    
                            if($feedback->author){
                                $thisFeedBack['AUTOR'] = $feedback->author->name;
                            } else {
                                $thisFeedBack['AUTOR'] = 'NULL';
                            }
                    
                            if($feedback->grades){
                                $thisFeedBack['RATING'] = (int)$feedback->grades->average;
                                $thisFeedBack['PLUS'] = (int)$feedback->grades->agreeCount;
                                $thisFeedBack['MINUS'] = (int)$feedback->grades->rejectCount;
                            }
                    
                            $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 54, 'XML_ID' => $thisFeedBack['ID']]);
                            if($ob = $rs->GetNext()){
                                \CIBlockElement::SetPropertyValuesEx($ob['ID'], 54, $thisFeedBack);
                            } else {
                                if($id = $el->Add([
                                    'IBLOCK_ID' => 54,
                                    'NAME' => "Отзыв {$thisFeedBack['AUTOR']} от {$thisFeedBack['DATE']}",
                                    'XML_ID' => $thisFeedBack['ID']
                                ])){
                                    \CIBlockElement::SetPropertyValuesEx($id, 54, $thisFeedBack);
                                }
                            }
                        }
                    } else {
                        $nextPageToken = $pageToken;
                    }
                } else {
                    $nextPageToken = $pageToken;
                }
            }
        }

        if($campaign['ID'] > 0){

            if(!empty($nextPageToken)){
                return "getYaReviewApi('{$campaign['ID']}', '{$nextPageToken}');";
            }
            
            if(count($arCamp) === 0){
                return 'getYaReviewApi("", "");';
            }

            $nextCamp = array_shift($arCamp);
            return "getYaReviewApi('{$nextCamp['ID']}', '');";

        } else {
            return 'getYaReviewApi("", "");';
        }
    }                       
}
?>