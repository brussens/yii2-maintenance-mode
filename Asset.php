<?php
/**
 * Maintenance mode component asset bundle.
 *
 * @package brussens\maintenance
 * @version 0.2.0
 * @author BrusSENS (Brusenskiy Dmitry) <brussens@nativeweb.ru>
 * @link https://github.com/brussens/yii2-maintenance-mode
 */

namespace brussens\maintenance;
use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@vendor/brussens/yii2-maintenance-mode/assets';

    public $css = [
        'css/styles.css',
    ];

    public $js = [];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}