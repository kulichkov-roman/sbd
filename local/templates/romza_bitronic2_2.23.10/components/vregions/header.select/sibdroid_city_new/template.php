<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?
$rand = rand();
$citySelectedId = 'rbs_city_name_header';
?>
<? if (!empty($arResult["ITEMS"])):?>
	<div class="city js-click">
		<a id="city-header-choose" class="city__selected js-click-button" href="javascript:void(0);">
			<span class="js-city-selected" id="<?=$citySelectedId?>">
				<?//$frame = $this->createFrame($citySelectedId, false)->begin('Новосибирск');?>
					<?=(strlen($arResult["CURRENT_SESSION_ARRAY"]["NAME"]) ? $arResult["CURRENT_SESSION_ARRAY"]["NAME"] : $arResult["DEFAULT"]["NAME"]);?>
				<?//$frame->end();?>
			</span>
		</a>
		<div class="city-hide js-click-hide">
			<button class="city-hide__button button-close js-click-close"></button>
			<p class="city-hide__title"><?=GetMessage('SELECT_YOUR_REGION')?></p>
			<p class="city-hide__total"><?=GetMessage('REGION_COUNT', ['#COUNT#' => count($arResult['ITEMS'])])?></p>
			<div class="city-form">
				<form id="city_search_form">
					<input name="city_search_field" class="city-form__input" type="text" placeholder="Поиск города">
					<button class="city-form__reset" type="reset"></button>
				</form>
			</div>
			<ul class="cities" id="cities_default">
				<?foreach($arResult["CHOSEN_ITEMS"] as $city):?>
					<li class="cities__item">
						<?// global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($city); echo '</pre>';}; ?>
						<a class="cities__link <?=$city['CODE']?:'dev'?>" href="<?=$city['HREF']?><?//=$_SERVER['REQUEST_URI']?>"><?=$city['NAME']?></a>
					</li>
				<?endforeach;?>
			</ul>
			<ul class="cities" id="cities_result" style="display:none;"></ul>
		</div>
	</div>
	<? //if ($arParams["SHOW_POPUP_QUESTION"] == "Y"){ ?>
		<? include "popup-question.php"; ?>
	<? //} ?>
	
<?endif;?>

<script>
	var citySearch = {
				
		init: function()
		{
			$('[name="city_search_field"]').on('keyup', this.onChange);
			$('[name="city_search_field"]').on('change', this.onChange);
			$('#city_search_form').on('reset', this.onReset);
			$('#cities_default a.cities__link').each(function(){
				$(this).attr('href', $(this).attr('href') + '/' + location.pathname.substring(1));
			});
		},

		onReset: function()
		{
			$('#cities_default').css({'display': 'block'});
			$('#cities_result').css({'display': 'none'});
		},

		onChange: function()
		{
			_t = this;
			$.ajax({
				url: '/ajax/sib/city_search.php',
				type: 'json',
				method: 'POST',
				data: {q: $('[name="city_search_field"]').val()},
				success: function(res)
				{
					if($('[name="city_search_field"]').val().length == 0)
					{
						$('#cities_default').css({'display': 'block'});
						$('#cities_result').css({'display': 'none'});
						return;
					}

					res = JSON.parse(res);
					if(res.COUNT == 0)
					{
						$('#cities_result').html('<li class="cities__item">Ничего не найдено. Попробуйте ввести снова.</li>')
					}
					else
					{
						$li = '';
						for(i in res.ITEMS)
						{
							$li += '<li class="cities__item">';
							if(res.ITEMS[i].CODE != ''){
								$li += '<a class="cities__link" href="https://' + res.ITEMS[i].CODE + '.sibdroid.ru' + window.location.pathname + '">' + res.ITEMS[i].NAME + '</a>';
							} else {
								$li += '<a class="cities__link" href="https://' + 'sibdroid.ru' + window.location.pathname + '">' + res.ITEMS[i].NAME + '</a>';
							}
							$li += '</li>';
						}
						$('#cities_result').html($li);
					}

					$('#cities_default').css({'display': 'none'});
					$('#cities_result').css({'display': 'block'});
				}	
			});
		}
	};

	citySearch.init();
</script>