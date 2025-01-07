<?php
/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

namespace humhub\modules\banner;

use humhub\modules\banner\assets\BannerAssets;
use humhub\modules\ui\view\components\View;
use Yii;
use yii\base\Event;
use yii\helpers\Url;

class Events
{
    public static function onViewBeginBody(Event $event)
    {
        if (
            Yii::$app->request->isConsoleRequest
            || Yii::$app->request->isAjax
        ) {
            return;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('banner');
        $configuration = $module->getConfiguration();

        if (!$configuration->enabled || !$configuration->content) {
            return;
        }

        /** @var View $view */
        $view = $event->sender;

        $view->registerJsConfig('banner', [
            'contentUrl' => Url::to(['/banner/index/index']),
        ]);

        BannerAssets::register($view);
    }
}
