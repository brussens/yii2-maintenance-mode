<?php
/**
 * Command for manage maintenance mode component for Yii framework 2.x.x version.
 *
 * @package brussens\maintenance\commands
 * @version 0.1.0
 * @author co11ter (Poltoratsky Alexander)
 * @link https://github.com/brussens/yii2-maintenance-mode
 */
namespace brussens\maintenance\commands;

use Yii;
use yii\console\Controller;

class MaintenanceController extends Controller
{
    public function actionIndex()
    {
        echo 'You have to input command "enable" or "disable"!'.PHP_EOL;
    }
    /**
     * Enable maintenance mode
     *
     * @return int
     */
    public function actionEnable()
    {
        if(!Yii::$app->maintenanceMode->enableProlonged()) {
            return Controller::EXIT_CODE_ERROR;
        }

        return Controller::EXIT_CODE_NORMAL;
    }
    /**
     * Disable maintenance mode
     *
     * @return int
     */
    public function actionDisable()
    {
        if(!Yii::$app->maintenanceMode->disableProlonged()) {
            return Controller::EXIT_CODE_ERROR;
        }

        return Controller::EXIT_CODE_NORMAL;
    }
}