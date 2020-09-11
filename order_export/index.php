<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
$arGroups = $USER->GetUserGroupArray();
$canEdit = false;
if(in_array(7, $arGroups) || $USER->IsAdmin()){
    $canEdit = true;
}
if(!$canEdit){
    die('Здесь ничего нет!');
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/_customs/PHPExcel/vendor/autoload.php');
 

$inProcess = false;
if(!empty($_GET) && count($_GET) > 0){
    $startDate = false;
    if(!empty($_GET['DATE_START'])){
        $startDate = new DateTime($_GET['DATE_START']);
    }
    $finishDate = false;
    if(!empty($_GET['DATE_START'])){
        $finishDate = new DateTime($_GET['DATE_FINISH']);
    }
    $step = (int)$_GET['STEP'];
    $stepSize = (int)$_GET['STEP_SIZE'] > 0 ? (int)$_GET['STEP_SIZE'] : 100;

    $getArr = $_GET;
    unset($getArr['STEP']);
    $fileName = 'z_report_' . md5(serialize($getArr)) . '.csv';
    $fileExcel = 'z_report_' . md5(serialize($getArr)) . '.xlsx';

    if($startDate && $finishDate && \Bitrix\Main\Loader::includeModule('sale')){
        $inProcess = true;
        $arFilter = [
            '>=DATE_INSERT' => $startDate->format('d.m.Y'),
            '<=DATE_INSERT' => $finishDate->format('d.m.Y'),
        ];
        $rs = \CSaleOrder::GetList(['ID' => 'ASC'], $arFilter, false, ['nPageSize' => $stepSize, 'iNumPage' => $step], ['ID']);
        $stepCount = ceil($rs->SelectedRowsCount() / $stepSize);

        if($step <= $stepCount){
            $arData = [];
            while ($ob = $rs->GetNext()) {
                $order = \Bitrix\Sale\Order::load($ob['ID']);
                $propertyCollection = $order->getPropertyCollection(); 
    
                $arData[$ob['ID']]['EMAIL'] = trim($propertyCollection->getUserEmail()->getValue());
                if(empty($arData[$ob['ID']]['EMAIL']) || !filter_var($arData[$ob['ID']]['EMAIL'], FILTER_VALIDATE_EMAIL)){
                    unset($arData[$ob['ID']]);
                    continue;
                }
    
                $locPropValue   = $propertyCollection->getDeliveryLocation()->getValue();            
    
                if(empty($locPropValue)){
                    $cityLoc = $propertyCollection->getItemByOrderPropertyId(5);
                    $loc = CSaleLocation::GetByID($cityLoc->getValue());
                    if(!$loc){
                        /* $loc['CITY_NAME_ORIG'] = $cityLoc; */
                        //echo '<pre>'; print_r($loc); echo '</pre>';
                        //break;
                    }
                } else {
                    $loc = CSaleLocation::GetByID($locPropValue);
                }
    
                if(empty($loc['CITY_NAME_ORIG'])){                
                    $arData[$ob['ID']]['CITY'] = $loc['REGION_NAME_ORIG'];
                    $arData[$ob['ID']]['ADDR'] = str_replace(';', ',', $propertyCollection->getItemByOrderPropertyId(7)->getValue());
                } else {
                    $arData[$ob['ID']]['CITY'] = $loc['CITY_NAME_ORIG'];
                }
    
                $arData[$ob['ID']] = implode(';', $arData[$ob['ID']]);
            }
            
            file_put_contents($fileName, implode("\n", $arData) . "\n", FILE_APPEND);
        }        
    }
}

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Экспорт заказов</title>
</head>
<body>
    <?if(!$inProcess):?>
        <form action="">
            <input type="date" name="DATE_START">
            <input type="date" name="DATE_FINISH">
            <input type="hidden" name="STEP" value="1">
            <label for="STEP_SIZE">Длина шага</label>
            <input type="number" name="STEP_SIZE" value="100" min="10" max="1000" step="10">
            <!-- <label for="STEP_SIZE">Отмененные заказы</label>
            <input type="checkbox" name="CANCLED">
            <label for="STEP_SIZE">Оплаченные заказы</label>
            <input type="checkbox" name="PAYED"> -->
            <input type="submit">
        </form>
    <?else:?>
        Шаг <?=$step?> из <?=$stepCount?> <br>
        <?
            $_GET['STEP'] = $_GET['STEP'] + 1;
            if($_GET['STEP'] <= $stepCount){
                $getNext = [];
                foreach($_GET as $k => $v){
                    $getNext[] = $k . '=' . $v;
                }
                header( "refresh:3;url=/order_export/?" . implode('&', $getNext)); 
            } else {

                $list = array();
                if (($fp = fopen($fileName, 'r')) !== false) {
                    while (($data = fgetcsv($fp, 0, ';')) !== false) {
                        $list[] = $data;
                    }
                    fclose($fp);
                }

                $xls = new PHPExcel();
 
                // В первый лист.
                $xls->setActiveSheetIndex(0);
                $sheet = $xls->getActiveSheet();
                
                // Формирование XLSX.
                $line = 0;
                foreach ($list as $line => $item) {
                    $line++;
                    foreach ($item as $col => $row) {
                        $sheet->setCellValueByColumnAndRow($col, $line, $row);
                    }
                }
                
                // Сохранение файла.
                $objWriter = new PHPExcel_Writer_Excel2007($xls);
                $objWriter->save($fileExcel);
            }
        ?>
    <?endif?>
</body>
</html>