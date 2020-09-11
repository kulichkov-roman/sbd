<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!$USER->IsAdmin()) die('permision denie');
CModule::IncludeModule('iblock');
$arSections=array();

$rsParentSection = CIBlockSection::GetByID(52);
if ($arParentSection = $rsParentSection->GetNext())
{
    $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'],'>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
    $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
    while ($arSect = $rsSect->GetNext())
    {
        $arSections[]=$arSect['ID'];
    }
}
$i=1;
$dbRes=CIBlockElement::GetList(array(),array('IBLOCK_ID'=>6,'ACTIVE'=>'Y','>CATALOG_QUANTITY'=>0),false,false,array('ID','NAME','IBLOCK_SECTION_ID','PROPERTY_rating','PROPERTY_vote_count','PROPERTY_vote_sum','CATALOG_QUANTITY'));
while($arRes=$dbRes->GetNext()){
    if($arRes['PROPERTY_RATING_VALUE']==0){
        /*echo "<pre>";
        print_r($arRes);
        echo "</pre>";*/
        echo $i.' RATING=0!!! '.$arRes['ID'].' '.$arRes['NAME'].' QUANTITY='.$arRes['CATALOG_QUANTITY'].'<br />';
        $cnt=rand(10,15);
        $summ=rand($cnt*4,$cnt*5);
        $rating=round(($summ+31.25/5*5)/($cnt+10),2);
        echo 'cnt='.$cnt.' summ='.$summ.' rating='.$rating.'<br />';

    }
    else
    {
        echo $i.' RATING>0 '.$arRes['ID'].' '.$arRes['NAME'].' QUANTITY='.$arRes['CATALOG_QUANTITY'].'<br />';
        echo 'lastcnt='.$arRes['PROPERTY_VOTE_COUNT_VALUE'].' summ='.$arRes['PROPERTY_VOTE_SUM_VALUE'].' rating='.$arRes['PROPERTY_RATING_VALUE'].'<br />';
        if(in_array($arRes['IBLOCK_SECTION_ID'],$arSections)){
            $cnt=rand(1,5);
        }else{
            $cnt=rand(0,1);
        }
        $summ=$arRes['PROPERTY_VOTE_SUM_VALUE']+rand($cnt*4,$cnt*5);
        $cnt=$arRes['PROPERTY_VOTE_COUNT_VALUE']+$cnt;
        $rating=round(($summ+31.25/5*5)/($cnt+10),2);
        echo 'newcnt='.$cnt.' summ='.$summ.' rating='.$rating.'<br />';
    }
    $i++;

}


?>