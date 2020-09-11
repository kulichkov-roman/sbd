<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if($USER->IsAdmin()):
echo "<pre>";
if (!CModule::IncludeModule("iblock"))
  die('exit!');
CModule::IncludeModule('yenisite.resizer2');

$rsElements = CIBlockElement::GetList(array("ID" => "DESC"), array("IBLOCK_ID" => 6, "ACTIVE"=>"Y"), false, array("nPageSize"=>1000, "iNumPage"=>0), array("ID", "NAME","DETAIL_PICTURE"));



// $iHeight = 468;
// $iWidth = 624;
// CResizer2Resize::ClearCacheByID(37);
while ($arElement = $rsElements->Fetch()) {
// var_dump($arElement);

  if ($arElement["DETAIL_PICTURE"] != "") {
    $arPreview = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);

    // $arPreview = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array('width' => $iWidth, 'height' => $iHeight), BX_RESIZE_IMAGE_PROPORTIONAL, false);

    $resized = CResizer2Resize::ResizeGD2($arPreview["SRC"], 37, 624, 468);
    // $resized = 'https://sibdroid.ru'.$resized;
    // var_dump($resized);
    // exit();
    // $arLoadProductArray = Array(
    //   // "DETAIL_PICTURE"  => CFile::MakeFileArray(CFile::GetPath($arElement["DETAIL_PICTURE"])),
    //   "PREVIEW_PICTURE" => CFile::MakeFileArray($arPreview["src"]),
    // );   

    
    $props = array(
    	'AVITO_PICTURE'=>'https://sibdroid.ru'.$resized
    );

    CIBlockElement::SetPropertyValuesEx($arElement["ID"], 6, $props);
    echo "Обновилась картинка у товара {$arElement["ID"]}<br />\n";

  }
  
}

?>
<?endif;?>