<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(method_exists($this, 'setFrameMode')) $this->setFrameMode(true);?>

<?
if ($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
	if ($arResult["PROPERTIES"]["vote_count"]["VALUE"])
		$votesValue = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
	else
		$votesValue = 0;
}
else
	$votesValue = round($arResult["PROPERTIES"]["RESP_QUANT"]["VALUE"]);

	//print_r($arResult["PROPERTIES"]["RESP_QUANT"]);
$votesCount = round($arResult["PROPERTIES"]["RESP_QUANT"]["VALUE"])?:0;
	
if (isset($arParams["AJAX_CALL"]) && $arParams["AJAX_CALL"]=="Y")
{
	$APPLICATION->RestartBuffer();

	die(json_encode( array(
		"value" => $votesValue,
		"votes" => $votesCount
		)
	));
}
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arResult["PROPERTIES"]); echo '</pre>';};
$templateData['~AJAX_PARAMS'] = $arResult['~AJAX_PARAMS'];

$bActive = !($arResult["VOTED"] || $arParams["READ_ONLY"]==="Y");
$onclick = "RZB2.ajax.Vote.do_vote(this, ".$arResult["AJAX_PARAMS"].", event)";
$votesValue = intval($votesValue);
?>
<div class="rating rating_big" data-rating="<?=$votesValue?>" data-itemid="<?=$arResult["ID"]?>" data-params="<?=$arResult['~AJAX_PARAMS']['SESSION_PARAMS']?>" data-disabled="<?=!$bActive ? 'true' : 'false'?>">
    <select class="js-rating" name="rating-16" autocomplete="off" style="display:none">
        <?foreach($arResult["VOTE_NAMES"] as $i => $name):?>
            <option value="<?=$i?>" data-value="<?=$i?>" data-index="<?=$i+1?>" title="<?=$name?>" <?if ($i == $votesValue):?>selected<?endif?>>
                <?=$i?>
            </option>
        <?endforeach?>
    </select>
</div>
