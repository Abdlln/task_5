<?php

namespace Dev\Site\Agents;

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;

class Iblock
{
    public static function clearOldLogs()
    {
        if (!Loader::includeModule('iblock')) {
            return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
        }

        $res = IblockTable::getList([
            'filter' => ['=CODE' => 'LOG'],
            'select' => ['ID'],
        ])->fetch();

        if (!$res) {
            return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
        }

        $logIblockId = (int) $res['ID'];

        $rsElements = \CIBlockElement::GetList(
            ['TIMESTAMP_X' => 'DESC'],
            ['IBLOCK_ID' => $logIblockId],
            false,
            false,
            ['ID']
        );

        $idsToDelete = [];
        $counter = 0;
        while ($el = $rsElements->Fetch()) {
            $counter++;
            if ($counter > 10) {
                $idsToDelete[] = $el['ID'];
            }
        }

        foreach ($idsToDelete as $id) {
            \CIBlockElement::Delete($id);
        }

        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}