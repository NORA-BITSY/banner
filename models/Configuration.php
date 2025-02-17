<?php
/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

namespace humhub\modules\banner\models;

use humhub\components\SettingsManager;
use humhub\libs\Html;
use Yii;
use yii\base\Model;

class Configuration extends Model
{
    public SettingsManager $settingsManager;

    public bool $enabled = false;
    public ?string $content = '';
    public bool $closeButton = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'closeButton'], 'boolean'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'enabled' => Yii::t('BannerModule.config', 'Enabled'),
            'content' => Yii::t('BannerModule.config', 'Banner content (HTML)'),
            'closeButton' => Yii::t('BannerModule.config', 'Close button'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $example =
'<style>
    :root {
        --banner-height: 60px;
        --banner-bg-color: red;
    }
</style>';

        return [
            'content' =>
                Yii::t('BannerModule.config', 'Examples of CSS values that can be overwritten:') . '<br>' .
                Html::tag('code', nl2br(str_replace(' ', '&nbsp;', Html::encode($example)))),
        ];
    }

    public function loadBySettings(): void
    {
        $this->enabled = (bool)$this->settingsManager->get('enabled', $this->enabled);
        $this->closeButton = (bool)$this->settingsManager->get('closeButton', $this->closeButton);
        $this->content = $this->settingsManager->get('content', $this->content);
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->settingsManager->set('enabled', $this->enabled);
        $this->settingsManager->set('closeButton', $this->closeButton);
        $this->settingsManager->set('content', $this->content);

        return true;
    }
}
