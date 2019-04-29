<?php

ini_set('date.timezone', 'UTC');

// Use the current installation of Craft
define('CRAFT_STORAGE_PATH', __DIR__ . '/_craft/storage');
define('CRAFT_TEMPLATES_PATH', __DIR__ . '/_craft/templates');
define('CRAFT_CONFIG_PATH', __DIR__ . '/_craft/config');
define('CRAFT_VENDOR_PATH', __DIR__ . '/../vendor');
define('CRAFT_FOLDER_PATH', __DIR__ . '/_craft');
define('CRAFT_SRC_PATH',dirname(__DIR__) . '/src');
define('ENV_PATH', __DIR__);
$devMode = true;

\craft\test\TestSetup::configureCraft();
