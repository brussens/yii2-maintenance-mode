<?php
/**
 * Default layout of maintenance mode component for Yii framework 2.x.x version.
 *
 * @version 0.2.0
 * @author BrusSENS (Brusenskiy Dmitry) <brussens@nativeweb.ru>
 * @link https://github.com/brussens/yii2-maintenance-mode
 */
use yii\helpers\Html;
use brussens\maintenance\Asset;

Asset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?php echo \Yii::$app->language; ?>">
    <head>
        <meta charset="<?php echo \Yii::$app->charset; ?>">
        <title><?php echo Html::encode(Yii::$app->name); ?></title>
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <section>
        <?php echo $content; ?>
    </section>
    <footer>
        <div class="container">
            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>