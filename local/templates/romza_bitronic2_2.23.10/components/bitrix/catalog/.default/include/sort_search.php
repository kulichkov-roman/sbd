<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if (!empty($arSort)): 
    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($sort); echo '</pre>';};?>
    <ul class="sorting sort-list">
        <? foreach ($arSort as $sortValue): ?>
            <li class="sorting__item <?= $sortValue == $sort['ACTIVE'] ? ($by == 'desc' ? 'active sort-down' : 'active') :''?>" data-sort="<?=$sortValue?>" data-sort-by="asc">
                <a class="sorting__link" href="#">
                    <?=GetMessage('BITRONIC2_CATALOG_SORT_BY_' . $sortValue)?>
                    <span class="icon-arrow-top"></span>
                </a>
            </li>
        <? endforeach ?>
    </ul>
    <div class="accessories-filter-mobile sort-list f-search-block">
        <div style="width:200px;">

            <div class="sec-choos-cont">
                <select class="rbs-sort search-sections">
                    <option value="0" data-name="Все товары" data-count=<?=count($arElements)?>>Все товары</option>
                    <? foreach ($arSecSearch as $obSec): ?>
                        <option value="<?=$obSec['ID']?>" data-name="<?=$obSec['NAME']?>" data-count=<?=count($sections[$obSec['ID']])?>> 
                            <?=$obSec['NAME']?> (<?=count($sections[$obSec['ID']])?>)
                        </option>
                    <? endforeach ?>
                    <span>test</span>
                </select>
                <div class="active-count"><?=count($arElements)?></div>
            </div>

            <select class="rbs-sort js-rbs-sort">
                <? foreach ($arSort as $sortValue): ?>
                    <?if($sortValue == 'name') continue;?>
                    <option data-sort="<?=$sortValue?>" data-sort-by="desc" <?= $sort['ACTIVE'] == $sortValue ? 'selected' : ''?>>
                        <?=GetMessage('BITRONIC2_CATALOG_SORT_BY_' . $sortValue . '_mobile_asc')?>
                    </option>
                    <?if($sortValue == 'price'):?>
                        <option data-sort="<?=$sortValue?>" data-sort-by="asc" <?= $by == 'asc' && $sort['ACTIVE'] == $sortValue ? 'selected' : ''?>> 
                            <?=GetMessage('BITRONIC2_CATALOG_SORT_BY_' . $sortValue . '_mobile_desc')?>
                        </option>
                    <?endif?>
                <? endforeach ?>
            </select>
            
        </div>
       <!--  <div class="f-btn js-filters"></div> -->
    </div>
    
<? endif ?>