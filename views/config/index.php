<?php
/**
 * Module Model
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

use humhub\modules\banner\models\Configuration;
use humhub\modules\banner\Module;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Button;
use yii\bootstrap\Alert;


/**
 * @var $this View
 * @var $model Configuration
 * @var $isActiveEvent bool
 */

/** @var Module $module */
$module = Yii::$app->getModule('banner');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= $module->getName() ?></strong>

        <div class="help-block">
            <?= $module->getDescription() ?>
        </div>
    </div>

    <div class="panel-body">
        <?php if ($isActiveEvent): ?>
            <?= Alert::widget([
                'body' => Yii::t('BannerModule.config', 'An event has been detected. This configuration may be overridden by the event.'),
                'options' => ['class' => 'alert-danger'],
            ]) ?>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['acknowledge' => true]); ?>
        <?= $form->field($model, 'enabled')->checkbox() ?>
        <?= $form->field($model, 'content')->textarea() ?>
        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>

    </div>
</div>
