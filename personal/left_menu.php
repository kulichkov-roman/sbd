<?
global $isNewTemplate;
$tmpl = 'user_left';
$menu = 'user';
if($isNewTemplate){
	$tmpl = 'sib_user_left';
	$menu = 'user_sib';
}

$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	$tmpl,
	array(
		"ROOT_MENU_TYPE" => $menu,
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"MENU_CACHE_TYPE" => "A",
		"CACHE_SELECTED_ITEMS" => "N",
		"MENU_CACHE_TIME" => "604800",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"DEFAULT_IMG_SRC" => "/new_img/avatar.png",
		"RESIZER_PERSONAL_AVATAR" => "8" //TODO
	),
	false
);
?>