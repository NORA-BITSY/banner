<?php
/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

namespace humhub\modules\banner\models;

use humhub\components\SettingsManager;
use Yii;
use yii\base\Model;

class Configuration extends Model
{
    public SettingsManager $settingsManager;

    public bool $enabled = false;
    public ?string $content = '';
    public ?string $contentGuests = '';
    public bool $closeButton = false;
    public string $style = 'info';
    public string $bannerType = BannerType::MANUAL;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'closeButton'], 'boolean'],
            [['content', 'contentGuests'], 'string'],
            ['style', 'in', 'range' => ['info', 'success', 'warning', 'danger']],
            ['bannerType', 'in', 'range' => BannerType::getTypes()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'enabled' => Yii::t('BannerModule.config', 'Enabled'),
            'content' => Yii::t('BannerModule.config', 'Banner content for logged-in users (HTML)'),
            'contentGuests' => Yii::t('BannerModule.config', 'Banner content for visitors / logged-out users (HTML)'),
            'closeButton' => Yii::t('BannerModule.config', 'Close button'),
            'style' => Yii::t('BannerModule.config', 'Style'),
            'bannerType' => Yii::t('BannerModule.config', 'Banner Type'),
        ];
    }

    public function attributeHints()
    {
        $contentHint = Yii::t('BannerModule.config', 'If the content is empty, the banner will not be displayed.');
        return [
            'content' => $contentHint,
            'contentGuests' => $contentHint,
        ];
    }

    public function loadBySettings(): void
    {
        $this->enabled = (bool)$this->settingsManager->get('enabled', $this->enabled);
        $this->closeButton = (bool)$this->settingsManager->get('closeButton', $this->closeButton);
        $this->content = $this->settingsManager->get('content', $this->content);
        $this->contentGuests = $this->settingsManager->get('contentGuests', $this->contentGuests);
        $this->style = $this->settingsManager->get('style', $this->style);
        $this->bannerType = $this->settingsManager->get('bannerType', $this->bannerType);
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->settingsManager->set('enabled', $this->enabled);
        $this->settingsManager->set('closeButton', $this->closeButton);
        $this->settingsManager->set('content', trim($this->content));
        $this->settingsManager->set('contentGuests', trim($this->contentGuests));
        $this->settingsManager->set('style', $this->style);
        $this->settingsManager->set('bannerType', $this->bannerType);

        return true;
    }
}
