<? //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($_SESSION["VREGIONS_REGION"]["NAME"]); echo '</pre>';}; ?>
<?if($_SERVER['HTTP_HOST'] != 'dev.sibdroid.ru:443'):
    $frame = $this->createFrame()->begin('');?>
    <div class="card-main-delivery">
        <div class="edost">
            
        <div class="order-delivery-town detail-delivery-town">
            <!-- <a href="javascript:void(0);" class="js-click-button">Нижний новгород</a> -->
            <!-- BEGIN CITY -->
                <div class="city city_delivery js-click">
                    <a class="rbs-detail-city city__selected" href="#"><span class="js-city-selected"><?=$arResult['CITY_NAME']?></span></a>
                    <div class="city-hide js-click-hide"></div>
                </div>
                
            <!-- CITY EOF -->
        </div>
            
            <div id="edost_catalogdelivery_inside"
                    data-id="<?= (isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : $arResult['ID']) ?>"
                    data-price="<?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?>" 
                    data-name="<?= str_replace(array('"', "'"), '&quot;', $arResult['NAME']) ?>">
            </div>
            <!-- <div id="edost_catalogdelivery_inside_detailed" class="hidden-xs"></div> -->
        </div>
    </div>
    <?$frame->end();?>
<?else:?>
<div class="card-main-delivery">
    <div class="edost">
        
    <div class="order-delivery-town detail-delivery-town">
        <!-- <a href="javascript:void(0);" class="js-click-button">Нижний новгород</a> -->
        <!-- BEGIN CITY -->
            <div class="city city_delivery js-click">
                <a class="rbs-detail-city city__selected" href="#"><span class="js-city-selected">Новосибирск</span></a>
                <div class="city-hide js-click-hide"></div>
            </div>
            
        <!-- CITY EOF -->
    </div>
        
        <div id="edost_catalogdelivery_inside" data-id="678157" data-name="Xiaomi Redmi Note 7 4Gb 64Gb Черный Global Version" style="display: block;"><ul class="edost edost_catalogdelivery_inside card-main-delivery-list"><li data-delivery-id="348" class="card-main-delivery-list__item"><span class="card-main-delivery-list__name"><span class="edost_format_tariff">Самовывоз</span></span><span class="card-main-delivery-list__price"><span class="rbs-delimiter-tariff"> - </span><a href="/contacts/" target="_blank" style="color: #006699;">ул. Новогодняя 17</a></span></li><li data-delivery-id="340" class="card-main-delivery-list__item"><span class="card-main-delivery-list__name"><span class="edost_format_tariff">Курьер</span> <span class="edost_format_name">(<span class="rbs-today-delivery js-card-question">сегодня <span class="card-tooltipe">При заказе до 19:00</span></span>)</span></span><span class="card-main-delivery-list__price"><span class="rbs-delimiter-tariff"> - </span>290 руб.</span></li></ul></div>
        <!-- <div id="edost_catalogdelivery_inside_detailed" class="hidden-xs"></div> -->
    </div>
</div>
<?endif?>