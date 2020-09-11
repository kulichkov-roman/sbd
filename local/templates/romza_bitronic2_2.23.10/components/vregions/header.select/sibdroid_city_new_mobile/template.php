<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?
$rand = rand();
$citySelectedId = 'rbs_city_name_header_mobile';
?>
<? if (!empty($arResult["ITEMS"])):?>
<a class="main-nav__link main-nav__link_loc js-nav-link js-nav-height" href="#">
	<span class="main-nav__fix">
		<span class="main-nav__text"><?=GetMessage('VREGION_CITY');?> <span id="<?=$citySelectedId?>">
				<?//$frame = $this->createFrame($citySelectedId, false)->begin('Новосибирск');?>
					<?=(strlen($arResult["CURRENT_SESSION_ARRAY"]["NAME"]) ? $arResult["CURRENT_SESSION_ARRAY"]["NAME"] : $arResult["DEFAULT"]["NAME"]);?>
				<?//$frame->end();?>
			</span>
		</span>
	</span>
</a>
<ul class="inner-nav js-nav-hide">
	<li class="city-hide" style="display: block;">
		<div class="inner-nav__head">
			<button class="button-back js-click-back normalize-height"></button>                                            
			<div class="inner-nav__head-txt">
				<p class="inner-nav__title"><?=GetMessage('SELECT_YOUR_REGION')?></p>
				<p class="inner-nav__total"><?=GetMessage('REGION_COUNT', ['#COUNT#' => count($arResult['ITEMS'])])?></p>
			</div>
			<button class="inner-nav__button button-close js-click-close"></button>
		</div>
		<div class="city-form">
			<form id="city_search_form_mobile">
				<input class="city-form__input" name="city_search_field_mobile" type="text" placeholder="<?=GetMessage('VREGION_CITY_SEARCH');?>">
				<button class="city-form__reset" type="reset"></button>
			</form>
		</div>
		<ul class="cities" id="cities_default_mobile">
			<?foreach($arResult["CHOSEN_ITEMS"] as $city):?>
				<li class="cities__item">
					<a class="cities__link <?=$city['CODE']?:'dev'?>" href="<?=$city['HREF']?>"><?=$city['NAME']?></a>
				</li>
			<?endforeach;?>
		</ul>
		<ul class="cities" id="cities_result_mobile" style="display:none;"></ul>
	</li>
</ul>
<?endif;?>

<script>
	var citySearcMobile = {
				
		init: function()
		{
			$('[name="city_search_field_mobile"]').on('keyup', this.onChange);
			$('[name="city_search_field_mobile"]').on('change', this.onChange);
			$('#city_search_form_mobile').on('reset', this.onReset);
			$('#cities_default_mobile a.cities__link').each(function(){
				$(this).attr('href', $(this).attr('href') + '/' + location.pathname.substring(1));
			});
		},

		onReset: function()
		{
			$('#cities_default_mobile').css({'display': 'block'});
			$('#cities_result_mobile').css({'display': 'none'});
		},

		onChange: function()
		{
			_t = this;
			$.ajax({
				url: '/ajax/sib/city_search.php',
				type: 'json',
				method: 'POST',
				data: {q: $('[name="city_search_field_mobile"]').val()},
				success: function(res)
				{
					if($('[name="city_search_field_mobile"]').val().length == 0)
					{
						$('#cities_default_mobile').css({'display': 'block'});
						$('#cities_result_mobile').css({'display': 'none'});
						return;
					}

					res = JSON.parse(res);
					if(res.COUNT == 0)
					{
						$('#cities_result_mobile').html('<li class="cities__item">Ничего не найдено. Попробуйте ввести снова.</li>')
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
							//$li += '<a class="cities__link" href="https://' + res.ITEMS[i].CODE + '.sibdroid.ru' + window.location.pathname + '">' + res.ITEMS[i].NAME + '</a>';
							$li += '</li>';
						}
						$('#cities_result_mobile').html($li);
					}

					$('#cities_default_mobile').css({'display': 'none'});
					$('#cities_result_mobile').css({'display': 'block'});
				}	
			});
		}
	};

	citySearcMobile.init();
</script>