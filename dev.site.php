<?php

class dev_site extends CModule
{
    const MODULE_ID = 'dev.site';

    public $MODULE_ID = 'dev.site';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME = 'Тренировочный модуль';
    public $PARTNER_NAME = 'dev';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/install/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    public function InstallFiles($arParams = [])
    {
        return true;
    }

    public function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        $this->InstallFiles();
    }

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
    }
}