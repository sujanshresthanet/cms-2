<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\log;

use Craft;
use Illuminate\Support\Collection;

/**
 * Class Dispatcher
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.6.0
 */
class Dispatcher extends \yii\log\Dispatcher
{
    /** @since 4.0.0 */
    public const TARGET_WEB = 'web';

    /** @since 4.0.0 */
    public const TARGET_CONSOLE = 'console';

    /** @since 4.0.0 */
    public const TARGET_QUEUE = 'queue';

    /**
     * @var array Config to pass to each MonologTarget
     * @since 4.0.0
     */
    public array $targetConfig = [];

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->targets = array_merge($this->getTargets(), $this->targets);
    }

    /**
     * @return MonologTarget[]
     */
    public function getTargets(): array
    {
        // Warning - Don't do anything that could cause something to get logged from here!
        // If the dispatcher is configured with flushInterval => 1, it could cause a PHP error if any log
        // targets haven’t been instantiated yet.

        $isConsoleRequest = Craft::$app->getRequest()->getIsConsoleRequest();

        // Only log console requests and web requests that aren't getAuthTimeout requests
        if (!$isConsoleRequest && !Craft::$app->getUser()->enableSession) {
            return [];
        }

        $targets = Collection::make([
            static::TARGET_WEB,
            static::TARGET_CONSOLE,
            static::TARGET_QUEUE,
        ])->mapWithKeys(function($name) {
            $config = array_merge($this->targetConfig, [
                'name' => $name,
                'enabled' => false,
            ]);

            return [$name => new MonologTarget($config)];
        });

        // Enabled via QueueLogBehavior
        if (!YII_DEBUG) {
            $queueTarget = $targets->get(static::TARGET_QUEUE);
            // TODO: Ask about except/levels
            $queueTarget->except = ['yii\*'];
            $queueTarget->setLevels(['info', 'warning', 'error']);
        }

        if ($isConsoleRequest) {
            $targets->get(static::TARGET_CONSOLE)->enabled = true;
        } else {
            $targets->get(static::TARGET_WEB)->enabled = true;
        }

        return $targets->all();
    }
}
