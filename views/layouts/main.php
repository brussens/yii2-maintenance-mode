<?php
/**
 * @link https://github.com/brussens/yii2-maintenance-mode
 * @copyright Copyright (c) 2017 Brusensky Dmitry
 * @license http://opensource.org/licenses/MIT MIT
 */

use yii\helpers\Html;
use brussens\maintenance\Asset;

/**
 * Default layout of maintenance mode component for Yii framework 2.x.x version.
 *
 * @since 0.2.0
 * @author Brusensky Dmity <brussens@nativeweb.ru>
 */

/** @var $this \yii\web\View */
/** @var $content string */

Asset::register($this);
?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language; ?>">
    <head>
        <meta charset="<?= \Yii::$app->charset; ?>">
        <title><?= Html::encode(Yii::$app->name); ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody(); ?>
    <section>
        <?= $content; ?>
    </section>
    <footer>
        <div class="container">
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>
    <?php $this->endBody(); ?>
    </body>
    </html>
<?php $this->endPage(); ?>
