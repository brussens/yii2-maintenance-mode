<?php
/**
 *
 */
namespace brussens\maintenance\commands;

use Yii;
use yii\console\Controller;

class MaintenanceController extends Controller
{
    public function actionIndex() {
        echo get_class(Yii::$app).PHP_EOL;
    }
}