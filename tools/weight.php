<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?if($USER->IsAdmin()):
    CModule::IncludeModule('iblock');
    ?>

    <h1>Товары и их вес</h1>
    <table width="100%" border="1" cellpadding="3" cellspacing="3">
    <tr>
        <th>артикул</th>
        <th>инфоблок</th>
        <th>товар</th>
        <th>вес</th>
        <th>длина</th>
        <th>ширина</th>
        <th>высота</th>
    </tr>

    <?
    global $DB;
    $arID=array();
    $dbRes=$DB->Query('SELECT ID FROM b_catalog_product  ORDER BY WEIGHT DESC');
    while($arRes=$dbRes->GetNext()){
        $arID[]=$arRes['ID'];
    }
    if(!empty($arID)){
        $dbRes = CIBlockElement::GetList(array(), array(
            "IBLOCK_ID"=>array(6),
            "ACTIVE"=>"Y",
            "ID"=>$arID,
        ), false, array('nTopCount'=>10000000), array("ID","CODE","NAME","CATALOG_GROUP_1","CATALOG_WEIGHT","CATALOG_WIDTH","CATALOG_LENGTH","CATALOG_HEIGHT","DETAIL_PAGE_URL","PROPERTY_CML2_ARTICLE"));
        while($arRes=$dbRes->GetNext()){ ?>
            <tr>
                <td><?=$arRes["PROPERTY_CML2_ARTICLE_VALUE"]?></td>
                <td><?=$arRes["IBLOCK_EXTERNAL_ID"]?></td>
            <td>
                <?if(!empty($arRes["DETAIL_PAGE_URL"])): ?>
                <a href="<?=$arRes["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arRes["NAME"]?></a></td>
            <? else: ?>
                <?=$arRes["NAME"]?>
                <? endif; ?>
                <td><?=$arRes["CATALOG_WEIGHT"]?></td>
                <td><?=$arRes["CATALOG_LENGTH"]?></td>
                <td><?=$arRes["CATALOG_WIDTH"]?></td>
                <td><?=$arRes["CATALOG_HEIGHT"]?></td>
            </tr>
        <?
        }
    }
    ?>
    </table>

    <h1>Товары без габаритов</h1>
    <table width="100%" border="1" cellpadding="3" cellspacing="3">
        <tr>
            <th>id</th>
            <th>инфоблок</th>
            <th>товар</th>
            <th>вес</th>
            <th>длина</th>
            <th>ширина</th>
            <th>высота</th>
        </tr>
        <?
            $arID=array();
            $dbRes=$DB->Query('SELECT ID FROM b_catalog_product WHERE WIDTH=0 OR LENGTH=0 OR HEIGHT=0');
            while($arRes=$dbRes->GetNext()){
                $arID[]=$arRes['ID'];
            }
            if(!empty($arID)){
                $dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>array(6),"ACTIVE"=>"Y",'ID'=>$arID), false, array('nTopCount'=>10000), array("ID","CODE","NAME","CATALOG_WEIGHT","CATALOG_WIDTH","CATALOG_LENGTH","CATALOG_HEIGHT","DETAIL_PAGE_URL","CATALOG_GROUP_1"));
                while($arRes=$dbRes->GetNext()){?>
            <tr>
                <td><?=$arRes["ID"]?></td>
                <td><?=$arRes["IBLOCK_TYPE_ID"]?></td>
                <td><a href="<?=$arRes["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arRes["NAME"]?></a></td>
                <td><?=$arRes["CATALOG_WEIGHT"]?></td>
                <td><?=$arRes["CATALOG_LENGTH"]?></td>
                <td><?=$arRes["CATALOG_WIDTH"]?></td>
                <td><?=$arRes["CATALOG_HEIGHT"]?></td>
            </tr>
                <?}
            }?>
    </table>
<? /*
    <h1>Услуги и их количество (quantity)</h1>
    <table width="100%" border="1" cellpadding="3" cellspacing="3">
        <tr>
            <th>#</th>
            <th>id</th>
            <th>инфоблок</th>
            <th>товар</th>
            <th>количество</th>

        </tr>
        <?
                $dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>array(6),"ACTIVE"=>"Y",'ID'=>$arID), false, array('nTopCount'=>10000), array("ID","CODE","NAME","CATALOG_QUANTITY","CATALOG_WIDTH","CATALOG_LENGTH","CATALOG_HEIGHT","DETAIL_PAGE_URL","CATALOG_GROUP_1"));
                while($arRes=$dbRes->GetNext()){?>
            <tr>
                <td><?=$i++?></td>
                <td><?=$arRes["ID"]?></td>
                <td><?=$arRes["IBLOCK_TYPE_ID"]?></td>
                <td><a href="<?=$arRes["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arRes["NAME"]?></a></td>
                <td><?=$arRes["CATALOG_QUANTITY"]?></td>

            </tr>
                <?}
            ?>
    </table>



    <h1>Товары и их xml id</h1>
    <table width="100%" border="1" cellpadding="3" cellspacing="3">
        <tr>
            <th>#</th>
            <th>id</th>
            <th>инфоблок</th>
            <th>товар</th>
            <th>количество</th>

        </tr>
        <?
        $dbRes = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>array(IBLOCK_PRODUCT),"ACTIVE"=>"Y",'ID'=>$arID), false, array('nTopCount'=>10000), array("ID","CODE","NAME","XML_ID","DETAIL_PAGE_URL","CATALOG_GROUP_1","PROPERTY_YANDEKS_ID_PRODUKTA"));
        while($arRes=$dbRes->GetNext()){?>
            <tr>

                <td><?=$arRes["ID"]?></td>
                <td><?=$arRes["XML_ID"]?></td>
                <td><a href="<?=$arRes["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arRes["NAME"]?></a></td>
                <td><?=$arRes["PROPERTY_YANDEKS_ID_PRODUKTA_VALUE"]?></td>

            </tr>
        <?}
        ?>
    </table>
*/ ?>
<? endif; ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
