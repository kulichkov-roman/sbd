<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?
$rand = rand();
if($_POST['ajax-city-hide'] == 'Y'){
	$_SERVER['REQUEST_URI'] = $_POST['request-uri']?:'';
}
?>
<?if(!empty($arResult["ITEMS"])):?>
	<div class="city-hide js-click-hide">
		<button class="city-hide__button button-close js-click-close"></button>
		<p class="city-hide__title"><?=GetMessage('SELECT_YOUR_REGION')?></p>
		<p class="city-hide__total"><?=GetMessage('REGION_COUNT', ['#COUNT#' => count($arResult['ITEMS'])])?></p>
		<div class="city-form">
			<form id="city_search_form_hide">
				<input class="city-form__input" name="city_search_field_hide" type="text" placeholder="Поиск города">
				<button class="city-form__reset" type="reset"></button>
			</form>
		</div>
		<ul id="cities_default_hide" class="cities">
			<?foreach($arResult["CHOSEN_ITEMS"] as $city):?>
				<li class="cities__item">
					<a class="cities__link <?=$city['CODE']?:'dev'?>" href="<?=$city['HREF']?>"><?=$city['NAME']?></a>
				</li>
			<?endforeach;?>
		</ul>
		<ul class="cities" id="cities_result_hide" style="display:none;"></ul>

			<script>
				var citySearchHide = {
							
					init: function()
					{
						$('[name="city_search_field_hide"]').on('keyup', this.onChange);
						$('[name="city_search_field_hide"]').on('change', this.onChange);
						$('#city_search_form_hide').on('reset', this.onReset);
						$('#cities_default_hide a.cities__link').each(function(){
							$(this).attr('href', $(this).attr('href') + '/' + location.pathname.substring(1));
						});
					},

					onReset: function()
					{
						$('#cities_default_hide').css({'display': 'block'});
						$('#cities_result_hide').css({'display': 'none'});
					},

					onChange: function()
					{
						_t = this;
						$.ajax({
							url: '/ajax/sib/city_search.php',
							type: 'json',
							method: 'POST',
							data: {q: $('[name="city_search_field_hide"]').val()},
							success: function(res)
							{
								if($('[name="city_search_field_hide"]').val().length == 0)
								{
									$('#cities_default_hide').css({'display': 'block'});
									$('#cities_result_hide').css({'display': 'none'});
									return;
								}

								res = JSON.parse(res);
								if(res.COUNT == 0)
								{
									$('#cities_result_hide').html('<li class="cities__item">Ничего не найдено. Попробуйте ввести снова.</li>')
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
									$('#cities_result_hide').html($li);
								}

								$('#cities_default_hide').css({'display': 'none'});
								$('#cities_result_hide').css({'display': 'block'});
							}	
						});
					}
				};

				citySearchHide.init();
			</script>

	</div>

<?endif;?>