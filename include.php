<?php

function dev_site_autoload($className)
{
    if (strpos($className, 'Dev\\Site\\') !== 0) {
        return;
    }

    $fileName = dirname(__FILE__) . '/lib/' . str_replace('\\', '/', $className) . '.php';

    if (file_exists($fileName)) {
        require_once $fileName;
    }
}

spl_autoload_register('dev_site_autoload');