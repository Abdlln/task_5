<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/modules/dev.site/include.php';

use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementAdd',
    [\Dev\Site\Handlers\Iblock::class, 'onAfterIblockElementChange']
);
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    [\Dev\Site\Handlers\Iblock::class, 'onAfterIblockElementChange']
);