<?php

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/general.php
 */

return array(

    'siteUrl' => $_ENV['SITE_URL'],
    'siteName' => $_ENV['SITE_NAME'],
    'environmentVariables' => array(
        'siteUrl' => $_ENV['SITE_URL'],
        'assetsUrl' => $_ENV['SITE_URL'].$_ENV['ASSETS_PATH'],
        'uploadsUrl' => $_ENV['SITE_URL'].$_ENV['UPLOADS_PATH'],
        'fileSystemPath' => $_ENV['BASE_DIR']
    ),
    'devMode' => $_ENV['DEV_MODE'],
    'enableTemplateCaching' => $_ENV['TEMPLATE_CACHING'],
    'backupDbOnUpdate' => $_ENV['BACKUP_DB_ON_UPDATE'],
    'cpTrigger' => $_ENV['ADMIN_PATH'],
    'omitScriptNameInUrls' => true,
    'errorTemplatePrefix' => '_errors/',
    'defaultImageQuality' => 85,
    'cacheDuration' => 'P1W',
    'sendPoweredByHeader' => false,
    'convertFilenamesToAscii' => true,
    'defaultSearchTermOptions' => array(
        'subLeft' => true,
        'subRight' => true
    )

);
