<?php
/**
 * Default controller of maintenance mode component for Yii framework 2.x.x version.
 * @package brussens\maintenance\controllers
 * @version 0.2.2
 * @author BrusSENS (Brusenskiy Dmitry) <brussens@nativeweb.ru>
 * @link https://github.com/brussens/yii2-maintenance-mode
 */
namespace brussens\maintenance\controllers;
use Yii;
use yii\web\Controller;
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
     *
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