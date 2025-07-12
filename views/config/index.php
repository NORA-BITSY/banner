<?php
/**
 * Module Model
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

use humhub\libs\Html;
use humhub\modules\banner\models\Configuration;
use humhub\modules\banner\Module;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\form\widgets\CodeMirrorInputWidget;
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
        <div class="alert alert-info cuzy-free-module-info">
            This module was created and is maintained by
            <a href="https://www.cuzy.app/"
               target="_blank">CUZY.APP (view other modules)</a>.
            <br>
            It's free, but it's the result of a lot of design and maintenance work over time.
            <br>
            If it's useful to you, please consider
            <a href="https://www.cuzy.app/checkout/donate/"
               target="_blank">making a donation</a>
            or
            <a href="https://github.com/cuzy-app/banner"
               target="_blank">participating in the code</a>.
            Thanks!
        </div>

        <?php if ($isActiveEvent): ?>
            <?= Alert::widget([
                'body' => Yii::t('BannerModule.config', 'An event has been detected. This configuration may be overridden by the event.'),
                'options' => ['class' => 'alert-danger'],
            ]) ?>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['acknowledge' => true]); ?>
        <?= $form->field($model, 'enabled')->checkbox() ?>
        <?= $form->field($model, 'closeButton')->checkbox() ?>
        <?= $form->field($model, 'style')->dropDownList([
            'info' => Yii::t('BannerModule.config', 'Info'),
            'success' => Yii::t('BannerModule.config', 'Success'),
            'warning' => Yii::t('BannerModule.config', 'Warning'),
            'danger' => Yii::t('BannerModule.config', 'Danger'),
        ]) ?>
        <?= $form->field($model, 'content')->widget(CodeMirrorInputWidget::class) ?>
        <?= $form->field($model, 'contentGuests')->widget(CodeMirrorInputWidget::class) ?>

        <?php $example = '<style>
    :root {
        --hh-banner-height: 60px; /** default 40px */
        --hh-banner-font-color: var(--text-color-contrast); /** default var(--text-color-contrast) */
        --hh-banner-bg-color: var(--danger); /** default var(--info) */
    }
</style>'; ?>

        <?= Yii::t('BannerModule.config', 'Examples of CSS values that can be overwritten:') ?>
        <br>
        <div>
            <code>
                <?= nl2br(str_replace(' ', '&nbsp;', Html::encode($example))) ?>
            </code>
        </div>
        <br>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>

    </div>
</div>
