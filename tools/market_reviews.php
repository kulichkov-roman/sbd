<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<main class="container about-page"><pre>
<?if($USER->isAdmin()):
$APPLICATION->AddChainItem("Инструменты", "/tools");
    CModule::IncludeModule('iblock');
// CIBlockElement::SetPropertyValues(  12859, 6, "1", "UPDATE_OPTIONS");
    ?>
<?

// CModule::IncludeModule('iblock');
//COption::SetOptionInt("main", "shop_rating", 5);
//COption::SetOptionInt("main", "shop_count_opinions", 51);
// COption::SetOptionInt("catalog", "YANDEX_OPINION_LOAD_STEP",0);
                        // $rsElementsO = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>IBLOCK_OPINIONS_PRODUCT), false, array('nTopCount'=>100000), array("ID"));
                        // while($obElementO = $rsElementsO->GetNext()){
                        //     datalog('yandexparser.log','Отзыв есть, очищаем: '.$obElementO['ID']);
                        //     CIBlockElement::Delete($obElementO['ID']);
                        //     $otziv++;
                        // }
// AgentYandexOpinionLoad(6);
                // $dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 6, '!PROPERTY_'.IBLOCK_YANDEX_ID_SHOP => false,"ACTIVE"=>"Y"), false, false, array("ID", "CODE", "NAME", "PROPERTY_".IBLOCK_YANDEX_ID_SHOP));
                // while ($arRes = $dbRes->GetNext()) {
                //     CIBlockElement::SetPropertyValues($arRes["ID"], 6, 1, "UPDATE_OPINIONS");
                // }

// $dbRes = CIBlockElement::GetList(array('property_RESP_COUNT'=>'desc'), array("IBLOCK_ID"=>6,'PROPERTY_UPDATE_OPINIONS'=>1,'!PROPERTY_'.IBLOCK_YANDEX_ID_SHOP => false,"ACTIVE"=>"Y"), false, array('nTopCount'=>200, "nPageSize"=>999), array("ID","CODE","NAME","PROPERTY_".IBLOCK_YANDEX_ID_SHOP,"PROPERTY_RESP_COUNT","PROPERTY_RESP_QUANT", "PROPERTY_RESP_QUANT", "PROPERTY_CML2_ARTICLE"));
//         while($arRes=$dbRes->GetNext()){
// $i++;
// CIBlockElement::SetPropertyValues($arRes["ID"], 6, 1, "UPDATE_OPINIONS");
//         }
//         var_dump($arRes);
?>
</pre>


    <h1>Товары и их отзывы на Яндекс.Маркете, их рейтинг</h1>
    <table width="100%" border="1" cellpadding="3" cellspacing="3">
        <tr>
            <th>#</th>
            <th>id</th>
            <th>ссылка</th>
            <th>маркет id</th>
            <th>количество отзывов</th>
            <th>отзывов в базе</th>
            <th>рейтинг</th>
            <th>нужно обновлять?</th>
        </tr>
        <?
        $i=1;
        $obnovit=0;
        $dbRes = CIBlockElement::GetList(array('property_RESP_COUNT'=>'desc'), array("IBLOCK_ID"=>array(IBLOCK_PRODUCT),"ACTIVE"=>"Y", '!PROPERTY_'.IBLOCK_YANDEX_ID_SHOP => false,'!PROPERTY_UPDATE_OPINIONS'=>2), false, array('nTopCount'=>100000), array("ID","CODE","NAME","DETAIL_PAGE_URL","CATALOG_GROUP_1","PROPERTY_".IBLOCK_YANDEX_ID_SHOP, "PROPERTY_RESP_COUNT", "PROPERTY_RESP_QUANT",'PROPERTY_UPDATE_OPINIONS'));
        while($arRes=$dbRes->GetNext()){
            $obnovit+=$arRes["PROPERTY_UPDATE_OPINIONS_VALUE"];
            $otziv=0;
            $rsElementsO = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>IBLOCK_OPINIONS_PRODUCT,'PROPERTY_PRODUCT_ID'=>$arRes["ID"]), false, array('nTopCount'=>100000), array("ID"));
                            while($obElementO = $rsElementsO->GetNext()){
                                $otziv++;
                            }

            ?>
            <tr>
                <td><?=$i++?></td>
                <td><?=$arRes["ID"]?></td>
                <td><a href="<?=$arRes["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arRes["NAME"]?></a></td>
                <td><?=$arRes["PROPERTY_".IBLOCK_YANDEX_ID_SHOP."_VALUE"]?></td>
                <td><?=$arRes["PROPERTY_RESP_COUNT_VALUE"]?></td>
                <td><?=$otziv?></td>
                <td><?=$arRes["PROPERTY_RESP_QUANT_VALUE"]?></td>
                <td><?=$arRes["PROPERTY_UPDATE_OPINIONS_VALUE"]?></td>
            </tr>
        <?}
        ?>
    </table><br>
К обновлению: <?=$obnovit?>

<? endif; ?>

</main>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
