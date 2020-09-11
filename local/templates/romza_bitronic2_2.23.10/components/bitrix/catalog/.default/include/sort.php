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
    <div class="accessories-filter-mobile sort-list">
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
        <div class="f-btn js-filters"></div>
    </div>
    
<? endif ?>