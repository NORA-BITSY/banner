<?php

/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

namespace humhub\modules\banner\controllers;

use humhub\components\Controller;
use humhub\components\Event;
use humhub\modules\banner\Module;

class IndexController extends Controller
{
    public const EVENT_BEFORE_RENDER_CONTENT = 'beforeRenderBannerContent';

    /**
     * List all available user groups
     */
    public function actionIndex()
    {
        /** @var Module $module */
        $module = $this->module;
        $configuration = $module->getConfiguration();

        $evt = new Event(['result' => $configuration->content]);
        Event::trigger($this, static::EVENT_BEFORE_RENDER_CONTENT, $evt);
        $content = $evt->result;

        return $this->renderPartial('index', [
            'content' => $content,
        ]);
    }
}
