<?php

namespace Aristov\VRegions;

use Bitrix\Main\Localization;

Localization\Loc::loadMessages(__FILE__);

class Texts{

    public static $moduleID = 'aristov.vregions';

    public static function getSectionText($sectionID){
        \CModule::IncludeModule('iblock');

        if ($sectionID){
            // ���� �� ������� ������������� �������� ���������?
            $res = \CIBlockElement::GetList(
                Array(
                    "SORT" => "ASC"
                ),
                Array(
                    'IBLOCK_TYPE'        => 'aristov_vregions_iblock_type',
                    'ACTIVE'             => 'Y',
                    'PROPERTY_CAT_ID'    => $sectionID,
                    'PROPERTY_REGION_ID' => $_SESSION['VREGIONS_REGION']['ID'],
                ),
                false,
                false,
                Array()
            );
            if ($ob = $res->GetNextElement()){
                $arFields = $ob->GetFields();

                // ���� � ����� �������� ���� ��������� �����, �� ���� ���
                if ($arFields["DETAIL_TEXT"]){
                    return html_entity_decode($arFields["DETAIL_TEXT"]);
                }
            }
        }

        return false;
    }

    public static function getElementText($elementID){
        \CModule::IncludeModule('iblock');

        if ($elementID){
            // ���� �� ������� ������������� �������� ���������?
            $res = \CIBlockElement::GetList(
                Array(
                    "SORT" => "ASC"
                ),
                Array(
                    'IBLOCK_TYPE'         => 'aristov_vregions_iblock_type',
                    'ACTIVE'              => 'Y',
                    'PROPERTY_ELEMENT_ID' => $elementID,
                    'PROPERTY_REGION_ID'  => $_SESSION['VREGIONS_REGION']['ID'],
                ),
                false,
                false,
                Array()
            );
            if ($ob = $res->GetNextElement()){
                $arFields = $ob->GetFields();

                // ���� � ����� �������� ���� ��������� �����, �� ���� ���
                if ($arFields["DETAIL_TEXT"]){
                    return html_entity_decode($arFields["DETAIL_TEXT"]);
                }
            }
        }

        return false;
    }

    public static function getTextByUrl($link = '', $fromPrev = false, $propCode = false){
        \CModule::IncludeModule('iblock');

        if (!$link){
            $link = $_SERVER['REDIRECT_URL'] ?: $_SERVER['REQUEST_URI'];
            if (strpos($link, '?')){
                $link = substr($link, 0, strpos($link, '?')); // �������� ��� ���������
            }
        }

        if ($_SESSION['VREGIONS_REGION']["ID"]){
            // ���� �� ������� ������������� �������� ���������?

            $arSelect = Array();
            if($propCode != ''){
                $arSelect = ['PROPERTY_' . $propCode];
            }

            $res = \CIBlockElement::GetList(
                Array(
                    "SORT" => "ASC"
                ),
                Array(
                    'IBLOCK_TYPE'        => 'aristov_vregions_iblock_type',
                    'ACTIVE'             => 'Y',
                    'PROPERTY_LINK'      => $link,
                    'PROPERTY_REGION_ID' => $_SESSION['VREGIONS_REGION']["ID"],
                ),
                false,
                false,
                $arSelect
            );
            if ($ob = $res->GetNextElement()){
                $arFields = $ob->GetFields();
                
                if($propCode){
                    return $arFields['PROPERTY_' . $propCode . '_VALUE']['TEXT'] ? htmlspecialchars_decode($arFields['PROPERTY_' . $propCode . '_VALUE']['TEXT']) : '';
                }

                if ($arFields["DETAIL_TEXT"] && !$fromPrev){
                    return html_entity_decode($arFields["DETAIL_TEXT"]);
                } else if($fromPrev){
                    return html_entity_decode($arFields["PREVIEW_TEXT"]);
                }
            }
        }

        return false;
    }
}