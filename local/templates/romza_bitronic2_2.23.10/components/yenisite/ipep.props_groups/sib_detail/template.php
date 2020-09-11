<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
} ?>
<? if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true); ?>
<?
$glueTemplate = ' / ';
foreach ($arResult['GROUPS'] as $groupID => $arGroup): ?>
	<div class="characteristics-item">
		<?if($groupID > 0):?>
			<div class="characteristics-item__title"><?=$arGroup['NAME']?></div>
		<?endif?>

		<ul class="characteristics-list">
			<?foreach ($arGroup['PROPS'] as $pid):
				$arProperty = $arResult['DISPLAY_PROPERTIES'][$pid];
				if(strpos($arProperty["CODE"], 'SIB_AVAIL') !== false || $arProperty["CODE"] == 'OFFERS_IN_REGION' || $arProperty["CODE"] == 'AVITO_PICTURE') continue;
				?>
					<li class="characteristics-list__item">
						<span class="characteristics-list__title">
							<span><?=$arProperty["NAME"]?></span>
							<?if(strlen($arProperty['HINT']) > 0):?>
								<span class="card-tooltipe-wrap js-card-question">
									<i class="icon-question"></i> 
									<span class="card-tooltipe"><?=$arProperty['HINT']?></span> 
								</span>   
							<?endif;?>
						</span>
						<span class="characteristics-list__value">
							<span>
								<?
									if ($arProperty['PROPERTY_TYPE'] == 'L'):
										if (is_array($arProperty['DISPLAY_VALUE'])):
											echo implode($glueTemplate, $arProperty['DISPLAY_VALUE']);
										else:
											echo $arProperty['DISPLAY_VALUE'];
										endif;
									else:
										if (is_array($arProperty["DISPLAY_VALUE"]) && $arProperty['PROPERTY_TYPE'] != 'F'):
											if($arProperty['PROPERTY_TYPE'] == 'E' || $arProperty['PROPERTY_TYPE'] == 'G') {
												foreach ($arProperty["DISPLAY_VALUE"] as &$p) {
													if (substr_count($p, "a href") > 0) {
														$p = strip_tags($p);
													}
												}
											}
											echo implode($glueTemplate, $arProperty["DISPLAY_VALUE"]);
										elseif ($arProperty['PROPERTY_TYPE'] == 'F'):
											if ($arProperty['MULTIPLE'] == 'Y'):
												if (is_array($arProperty['DISPLAY_VALUE'])):
													foreach ($arProperty['DISPLAY_VALUE'] as $n => $value):
														echo $n > 0 ? ', ' : '';
														echo str_replace('</a>', ' ' . $arProperty['DESCRIPTION'][$n] . '</a>', $value);
													endforeach;
												else:
													echo str_replace('</a>', ' ' . $arProperty['DESCRIPTION'][0] . '</a>', $arProperty['DISPLAY_VALUE']);
												endif;
											else:
												echo str_replace('</a>', ' ' . $arProperty['DESCRIPTION'] . '</a>', $arProperty['DISPLAY_VALUE']);
											endif;
										else:
											//if (substr_count($arProperty["DISPLAY_VALUE"], "a href") > 0) {
											if ($arProperty['PROPERTY_TYPE'] == 'E' || $arProperty['PROPERTY_TYPE'] == 'G') {
												$arProperty["DISPLAY_VALUE"] = strip_tags($arProperty["DISPLAY_VALUE"]);
											}
											
											echo $arProperty["DISPLAY_VALUE"];
											
											// TODO DESCRIPTION
											if (false && $arParams['SHOW_PROPERTY_VALUE_DESCRIPTION'] != 'N') {
												echo ' ', $arProperty['DESCRIPTION'];
											}
										endif;
									endif;
									?>
								</span>
						</span>
					</li>
			<? endforeach ?>
		</ul>
	</div><!-- .tech-info-block -->
<? endforeach ?>
		
<?
// echo "<pre style='text-align:left;'>";print_r($arResult);echo "</pre>";
