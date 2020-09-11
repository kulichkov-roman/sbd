<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<ul class="nav-tab-list">
    <li class="nav-tab-list__item <?= $view == 'blocks' ? 'active' : '' ?>">
        <a href="#tab_2" class="nav-tab-list__link" data-view="blocks"><span class="icon-icon-grid"></span></a>
    </li>
    <li class="nav-tab-list__item <?= $view == 'list' ? 'active' : '' ?>">
        <a href="#tab_1" class="nav-tab-list__link" data-view="list"><span class="icon-icon-list"></span></a>
    </li>    
</ul>