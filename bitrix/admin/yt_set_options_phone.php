<?
use \Bitrix\Main\Config\Configuration;
use \Bitrix\Main\Config\Option;

define('ADMIN_MODULE_NAME', 'main');

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

$configuration = Configuration::getInstance();

IncludeModuleLangFile(__FILE__);

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

$aTabs = array(
    array(
        'DIV' => 'main',
        'TAB' => 'Настройки',
        'ICON' => 'main_user_edit',
        'TITLE' =>'Установка опций для смартфонов',
    ),
);

$tabControl = new CAdminTabControl('tabControl', $aTabs);

if($REQUEST_METHOD == 'POST' && ($save!='' || $apply!='') && $isAdmin && check_bitrix_sessid())
{
    /*
     * Установка дефолтных опций в свойства смартфонов
     * */
    $arSort = array();
    $arSelect = array(
        'ID',
        'NAME',
        'PROPERTY_SERVICE'
    );
    $arFilter = array(
        'IBLOCK_ID' => $configuration->get('catalogIBlockId'),
        'SECTION_ID' => $configuration->get('catalogPhoneSectionId'),
        'INCLUDE_SUBSECTIONS' => 'Y'
    );

    $rsElements = \CIBlockElement::GetList(
        $arSort,
        $arFilter,
        false,
        false,
        $arSelect
    );

    /*
     * Карта с опциями товаров
     * */
    $arOptionsMap = array();
    while ($arItem = $rsElements->Fetch())
    {
        $arOptionsMap[$arItem['ID']][] = $arItem['PROPERTY_SERVICE_VALUE'];
    }

    if(!empty($arOptionsMap))
    {
        /*
         * Опции по-умолчанию
         * */
        $arDefaultOptions = array('SERVICE' => Option::get('askaron.settings', 'UF_PHONE_OPTIONS'));

        if(!empty($arDefaultOptions))
        {
            $iblockId = $configuration->get('catalogIBlockId');

            foreach ($arOptionsMap as $id => $arElementItem)
            {
                \CIBlockElement::SetPropertyValuesEx(
                    $id,
                    $iblockId,
                    $arDefaultOptions
                );
            }
        }
    }

    if($save!='' && $_GET['return_url']!='')
        LocalRedirect($_GET['return_url']);
    LocalRedirect('/bitrix/admin/yt_set_options_phone.php?lang='.LANGUAGE_ID.($return_url ? '&res=ok&return_url='.urlencode($_GET['return_url']): '').'&res=ok&'.$tabControl->ActiveTabParam());
}

$APPLICATION->SetTitle('Установка опций для смартфонов');

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

?>
<form method='POST' action='yt_set_options_phone.php?lang=<?echo LANGUAGE_ID?><?echo $_GET['return_url']? '&amp;return_url='.urlencode($_GET['return_url']): ''?>'  enctype='multipart/form-data' name='editform'>
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td colspan='2'>
            <?
            if($_REQUEST["res"] == "ok")
            {
                \CAdminMessage::ShowMessage(
                    array(
                        'MESSAGE' => 'Для всех смартфонов установлены опции по-умолчанию.',
                        'TYPE' => 'OK'
                    )
                );
            }
            ?>
            <?echo BeginNote();?>Запустите скрипт установки опций для смартфонов. Список опций по-умолчанию редактируется в модуле <a target="_blank" href="<?=$configuration->get('optionsPlusAdminPageUrl')?>">Настройки++</a>.
            <?echo EndNote(); ?>
        </td>
    </tr>
    <?
    $tabControl->Buttons();
    ?>
    <input type="submit" name="save" value="Запустить"/>&nbsp;
    <?echo bitrix_sessid_post();?>
    <input type='hidden' name='lang' value='<?echo LANG?>'>
    <?
    $tabControl->End();
    ?>
</form>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>
