<?php

namespace Dev\Site\Handlers;

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;
use Bitrix\Iblock\IblockTable;

class Iblock
{
    public static function onAfterIblockElementChange($arFields)
    {
        $iblockId = (int) $arFields['IBLOCK_ID'];
        $elementId = (int) $arFields['ID'];

        if (!Loader::includeModule('iblock')) {
            return;
        }

        $res = IblockTable::getList([
            'filter' => ['=CODE' => 'LOG'],
            'select' => ['ID'],
        ])->fetch();

        if (!$res) {
            return;
        }

        $logIblockId = (int) $res['ID'];

        if ($iblockId === $logIblockId) {
            return;
        }

        $iblock = IblockTable::getById($iblockId)->fetch();
        if (!$iblock) {
            return;
        }
        $iblockName = $iblock['NAME'];
        $iblockCode = $iblock['CODE'];

        $elementName = $arFields['NAME'] ?? "Элемент #$elementId";

        $sectionPath = [];
        $sectionId = (int) ($arFields['IBLOCK_SECTION_ID'] ?? 0);
        while ($sectionId > 0) {
            $section = \CIBlockSection::GetByID($sectionId)->GetNext();
            if (!$section) {
                break;
            }
            array_unshift($sectionPath, $section['NAME']);
            $sectionId = (int) $section['IBLOCK_SECTION_ID'];
        }

        $previewText = $iblockName;
        if (!empty($sectionPath)) {
            $previewText .= ' -> ' . implode(' -> ', $sectionPath);
        }
        $previewText .= ' -> ' . $elementName;

        $logSectionId = self::findOrCreateLogSection($logIblockId, $iblockName, $iblockCode);

        $el = new \CIBlockElement();
        $rsLog = $el->GetList(
            [],
            [
                'IBLOCK_ID' => $logIblockId,
                'NAME' => (string) $elementId,
                'IBLOCK_SECTION_ID' => $logSectionId,
            ],
            false,
            false,
            ['ID']
        );

        $existingLog = $rsLog->Fetch();

        $logData = [
            'IBLOCK_ID' => $logIblockId,
            'IBLOCK_SECTION_ID' => $logSectionId,
            'NAME' => (string) $elementId,
            'ACTIVE_FROM' => date('d.m.Y H:i:s'),
            'PREVIEW_TEXT' => $previewText,
            'PREVIEW_TEXT_TYPE' => 'text',
        ];

        if ($existingLog) {
            $el->Update($existingLog['ID'], $logData);
        } else {
            $el->Add($logData);
        }
    }

    private static function findOrCreateLogSection($logIblockId, $name, $code)
    {
        $section = SectionTable::getList([
            'filter' => [
                'IBLOCK_ID' => $logIblockId,
                '=CODE' => $code,
            ],
            'select' => ['ID'],
        ])->fetch();

        if ($section) {
            return (int) $section['ID'];
        }

        $bs = new \CIBlockSection();
        $sectionId = $bs->Add([
            'IBLOCK_ID' => $logIblockId,
            'NAME' => $name,
            'CODE' => $code,
        ]);

        return $sectionId ?: 0;
    }
}