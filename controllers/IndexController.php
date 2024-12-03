<?php

/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

namespace humhub\modules\banner\controllers;

use humhub\components\Controller;
use humhub\modules\banner\Module;

class IndexController extends Controller
{
    public function actionIndex()
    {
        /** @var Module $module */
        $module = $this->module;
        $configuration = $module->getConfiguration();

        return $this->renderPartial('index', [
            'content' => $configuration->content,
        ]);
    }
}
