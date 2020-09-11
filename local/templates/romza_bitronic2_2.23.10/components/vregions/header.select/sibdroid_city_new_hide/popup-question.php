<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="location">
	<div class="location__fix-1">
    	<div class="location__fix-2">
        	<div class="location__content">
            	<button class="location__close button-close js-close-loc rbs-city-choose-close"></button>
                <p class="location__title">Ваш город:</p>
                <p class="location__city"><?=(strlen($arResult["CURRENT_SESSION_ARRAY"]["NAME"]) ? $arResult["CURRENT_SESSION_ARRAY"]["NAME"] : $arResult["DEFAULT"]["NAME"]);?></p>
                <div class="location__cols">
                	<div class="location__col">
                    	<button class="location__button button button_white button_border" onclick="$('.rbs-city-choose-close').click();$('#city-header-choose').click();">Выбрать другой</button>
                    </div>
                	<div class="location__col">
                    	<button class="location__button button button_white js-close-loc">Все верно!</button>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
    <a class="location__mask js-close-loc" href="javascript:void(0);"></a>
</div>