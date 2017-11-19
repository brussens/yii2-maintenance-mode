<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) 2017 Brusensky Dmitry
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Console controller for manage maintenance mode component for Yii framework 2.x.x version.
 *
 * @see \yii\console\Controller
 * @package brussens\maintenance\commands
 * @author Poltoratsky Alexander
 * @since 0.2.2
 */
class MaintenanceController extends Controller
{
    /**
     *  Default action of controller.
     */
    public function actionIndex()
    {
        echo 'You have to input command "enable" or "disable"!' . PHP_EOL;
    }

    /**
     * Enable maintenance mode.
     * @since 0.2.2
     * @return int
     */
    public function actionEnable()
    {
        $maintenance = Yii::$app->maintenanceMode;

        if (!$maintenance->getIsEnabled(true) && $maintenance->enable()) {
            $this->stdout("Maintenance mode enabled successfully.\n", Console::FG_GREEN);
            return Controller::EXIT_CODE_NORMAL;
        } else {
            $this->stdout("Maintenance mode already enabled.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }
    }
    /**
     * Disable maintenance mode.
     * @since 0.2.2
     * @return int
     */
    public function actionDisable()
    {
        $maintenance = Yii::$app->maintenanceMode;

        if ($maintenance->getIsEnabled(true) && $maintenance->disable()) {
            $this->stdout("Maintenance mode disabled successfully.\n", Console::FG_GREEN);
            return Controller::EXIT_CODE_NORMAL;
        } else {
            $this->stdout("Maintenance mode already disabled.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }
    }
}