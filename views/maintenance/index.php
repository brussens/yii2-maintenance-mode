<?php
/**
 * Default view of maintenance mode component for Yii framework 2.x.x version.
 *
 * @version 0.2.0
 * @author BrusSENS (Brusenskiy Dmitry) <brussens@nativeweb.ru>
 * @link https://github.com/brussens/yii2-maintenance-mode
 */
use yii\helpers\Html;
?>
<h1>We&rsquo;ll be back soon!</h1>
<div>
    <p>
        <?php if (Yii::$app->maintenanceMode->message): ?>

            <?php echo Yii::$app->maintenanceMode->message; ?>

        <?php else: ?>

            Sorry for the inconvenience but we’re performing some maintenance at the moment.
            If you need to you can always <?= Html::mailto('contact us', (\Yii::$app->params['adminEmail'] ? \Yii::$app->params['adminEmail'] : '#')) ?>,
            otherwise we’ll be back online shortly!

        <?php endif; ?>
    </p>
</div>