<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.github.io/license/
 */
namespace craft\test;

use craft\db\Connection;
use craft\db\Migration;
use craft\db\MigrationManager;
use craft\helpers\MigrationHelper;
use craft\migrations\Install;
use craft\models\Site;
use craft\web\UploadedFile;
use Dotenv\Dotenv;
use yii\base\InvalidArgumentException;
use yii\db\Exception;

/**
 * Class TestSetup.
 *
 *  TODO:This class will be rewritten to support plugins. The way it currently set's up the install migration
 *  will be altered.
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @author Global Network Group | Giel Tettelaar <giel@yellowflash.net>
 * @since  3.0
 */
class TestSetup
{
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var bool
     */
    private $hasBeenCleansed = false;

    /**
     * Creates a craft object to play with. Ensures the Craft::$app service locator is working.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function warmCraft()
    {
        $app = require dirname(__DIR__, 2).'/tests/_craft/config/test.php';
        $app['isInstalled'] = false;

        return \Craft::createObject($app);
    }

    /**
     * Taken from the Yii2 Module $i->_after
     */
    public static function tearDownCraft()
    {
        $_SESSION = [];
        $_FILES = [];
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_REQUEST = [];

        UploadedFile::reset();
        if (method_exists(\yii\base\Event::class, 'offAll')) {
            \yii\base\Event::offAll();
        }
        \Craft::setLogger(null);

        \Craft::$app = null;

    }

    /**
     * @return bool
     * @throws Exception
     */
    public function clenseDb()
    {
        $tables = $this->connection->schema->getTableNames();

        foreach ($tables as $table) {
            MigrationHelper::dropTable($table);
        }


        $tables = $this->connection->schema->getTableNames();
        if ($tables !== []) {
            throw new Exception('Unable to setup test enviroment');
        }

        $this->hasBeenCleansed = true;
        return true;
    }

    /**
     * @param Migration $migration
     * @return false|null
     * @throws \Throwable
     */
    public function validateAndApplyMigration(string $class, array $params)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException('Unable to ');
        }

        $migration = new $class($params);

        if (!$migration instanceof Migration) {
            throw new InvalidArgumentException('Migration class is not an instance of craft\migrations\Migration');
        }

        return $migration->safeUp();
    }

    public static function configureCraft()
    {
        define('YII_ENV', 'test');

        $vendorPath = realpath(CRAFT_VENDOR_PATH);
        $craftPath = CRAFT_FOLDER_PATH;

        $configPath = realpath($craftPath.'/config');
        $contentMigrationsPath = realpath($craftPath.'/migrations');
        $storagePath = realpath($craftPath.'/storage');
        $templatesPath = realpath($craftPath.'/templates');
        $translationsPath = realpath($craftPath.'/translations');

        // Log errors to craft/storage/logs/phperrors.log
        ini_set('log_errors', 1);
        ini_set('error_log', $storagePath . '/logs/phperrors.log');

        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        defined('YII_DEBUG') || define('YII_DEBUG', true);
        defined('YII_ENV') || define('YII_ENV', 'dev');
        defined('CRAFT_ENVIRONMENT') || define('CRAFT_ENVIRONMENT', '');

        defined('CURLOPT_TIMEOUT_MS') || define('CURLOPT_TIMEOUT_MS', 155);
        defined('CURLOPT_CONNECTTIMEOUT_MS') || define('CURLOPT_CONNECTTIMEOUT_MS', 156);

        $libPath = dirname(__DIR__, 2) . '/lib';

        $srcPath  = dirname(__DIR__);

        require $vendorPath . '/yiisoft/yii2/Yii.php';
        require $srcPath.'/Craft.php';

        if (file_exists(ENV_PATH.'/.env')) {
            (new Dotenv(ENV_PATH))->load();
        }

        // Set aliases
        \Craft::setAlias('@lib', $libPath);
        \Craft::setAlias('@craft', $srcPath);
        \Craft::setAlias('@config', $configPath);
        \Craft::setAlias('@contentMigrations', $contentMigrationsPath);
        \Craft::setAlias('@storage', $storagePath);
        \Craft::setAlias('@templates', $templatesPath);
        \Craft::setAlias('@translations', $translationsPath);
    }

    /**
     * @return void
     */
    public function setupCraftDb()
    {
        if ($this->connection->schema->getTableNames() !== []) {
            throw new Exception('Not allowed to setup the DB if it hasnt been cleansed');
        }

        $site = new Site([
            'name' => 'Craft test site',
            'handle' => 'default',
            'hasUrls' => true,
            'baseUrl' => 'https://craftcms.com',
            'language' => 'en-US',
            'primary' => true,
        ]);

        $migration = new Install([
            'db' => $this->connection,
            'username' => 'craftcms',
            'password' => 'craftcms2018!!',
            'email' => 'support@craftcms.com',
            'site' => $site,
        ]);

        return $migration->safeUp();
    }

}
