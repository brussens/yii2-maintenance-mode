<?php
/**
 * Command for manage maintenance mode component for Yii framework 2.x.x version.
 * @package brussens\maintenance\commands
 * @version 0.2.0
 * @author co11ter (Poltoratsky Alexander)
 * @link https://github.com/brussens/yii2-maintenance-mode
 */
namespace brussens\maintenance\commands;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
class MaintenanceController extends Controller
{
    public function actionIndex()
    {
        echo 'You have to input command "enable" or "disable"!'.PHP_EOL;
    }
    /**
     * Enable maintenance mode
     * @return int
     */
    public function actionEnable()
    {
        $maintenance = Yii::$app->maintenanceMode;
        if(!$maintenance->getIsEnabled(true) && $maintenance->enable()) {
            $this->stdout("Maintenance mode enabled successfully.\n", Console::FG_GREEN);
            return Controller::EXIT_CODE_NORMAL;
        }
        else {
            $this->stdout("Maintenance mode already enabled.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }
    }
    /**
     * Disable maintenance mode
     * @return int
     */
    public function actionDisable()
    {
        $maintenance = Yii::$app->maintenanceMode;
        if($maintenance->getIsEnabled(true) && $maintenance->disable()) {
            $this->stdout("Maintenance mode disabled successfully.\n", Console::FG_GREEN);
            return Controller::EXIT_CODE_NORMAL;
        }
        else {
            $this->stdout("Maintenance mode already disabled.\n", Console::FG_RED);
            return Controller::EXIT_CODE_ERROR;
        }
    }
}