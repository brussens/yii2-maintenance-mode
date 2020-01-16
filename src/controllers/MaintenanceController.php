<?php

namespace app\controllers;

use yii\base\Controller;

/**
 * Class MaintenanceController
 *
 * Will be placed in your application automatically when
 * following the installation instructions.
 *
 * @package brussens\maintenance\controllers
 */
class MaintenanceController extends Controller
{
    /**
     * Render the standard maintenance view provided with
     * brussens/yii2-maintenance.
     *
     * If you want to render your own, custom maintenance page,
     * you can * define it in your application
     * 'container'->'singletons' configuration 'route' parameter.
     *
     * @see README.md for an example on how to configure it.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'maintenance';
        return $this->render('maintenance');
    }

}
