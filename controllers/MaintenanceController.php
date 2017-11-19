<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) 2017 Brusensky Dmitry
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace brussens\maintenance\controllers;

use Yii;
use yii\web\Controller;

/**
 * Default controller of maintenance mode component for Yii framework 2.x.x version.
 *
 * @see \yii\web\Controller
 * @package brussens\maintenance\controllers
 * @author Brusensky Dmitry <brussens@nativeweb.ru>
 * @since 0.2.0
 */
class MaintenanceController extends Controller
{
    /**
     * Initialize controller.
     */
    public function init()
    {
        $this->layout = Yii::$app->maintenanceMode->layoutPath;
        parent::init();
    }

    /**
     * Index action.
     * @return bool|string
     */
    public function actionIndex()
    {
        $app = Yii::$app;

        if ($app->getRequest()->getIsAjax()) {
            return false;
        }

        return $this->render($app->maintenanceMode->viewPath, [
            'title' => $app->maintenanceMode->title,
            'message' => $app->maintenanceMode->message
        ]);
    }
} 