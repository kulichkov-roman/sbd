<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(method_exists($this, 'setFrameMode')) $this->setFrameMode(true);?>

<?
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info.php';

if($arParams["DISPLAY_AS_RATING"] == "vote_avg")
{
	if($arResult["PROPERTIES"]["vote_count"]["VALUE"])
		$votesValue = round($arResult["PROPERTIES"]["vote_sum"]["VALUE"]/$arResult["PROPERTIES"]["vote_count"]["VALUE"], 2);
	else
		$votesValue = 0;
}
else
	$votesValue = round($arResult["PROPERTIES"]["rating"]["VALUE"]);

$votesCount = intval($arResult["PROPERTIES"]["vote_count"]["VALUE"]);
	
if(isset($arParams["AJAX_CALL"]) && $arParams["AJAX_CALL"]=="Y")
{
	$APPLICATION->RestartBuffer();

	die(json_encode( array(
		"value" => $votesValue,
		"votes" => $votesCount
		)
	));
}

$templateData['~AJAX_PARAMS'] = $arResult['~AJAX_PARAMS'];

$bActive = !($arResult["VOTED"] || $arParams["READ_ONLY"]==="Y");
$bNotRated = ($votesCount < 1);
$onclick = "RZB2.ajax.Vote.do_vote(this, ".$arResult["~AJAX_PARAMS"].", event)";
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($votesValue); echo '</pre>';};
?>
	<meta itemprop="ratingCount" content="<?= $votesCount ?>">
	<meta itemprop="ratingValue" content="<?= intval($votesValue) ?>">
	<meta itemprop="worstRating" content="0">
	<div class="" data-rating="<?=$votesValue?>" data-itemid="<?=$arResult["ID"]?>" data-params="<?=$arResult['~AJAX_PARAMS']['SESSION_PARAMS']?>" data-disabled="<?=!$bActive ? 'true' : 'false'?>">
		<select class="js-rating" name="user-rating-1" autocomplete="off">
			<? foreach ($arResult["VOTE_NAMES"] as $i => $name): ?>
				<option value="<?=$i?>" <?=$votesValue == $i ? 'selected' : ''?>><?=$name?></option>
			<? endforeach ?>
		</select>
	</div>
