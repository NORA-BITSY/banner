<?php
/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

namespace humhub\modules\banner;

use humhub\modules\banner\models\Configuration;
use Yii;
use yii\helpers\Url;

/**
 *
 * @property-read mixed $configUrl
 * @property-read Configuration $configuration
 * @property-read string[] $notifications
 */
class Module extends \humhub\components\Module
{
    /**
     * @var string defines the icon
     */
    public $icon = 'eye';

    /**
     * @var string defines path for resources, including the screenshots path for the marketplace
     */
    public $resourcesPath = 'resources';

    private ?Configuration $_configuration = null;

    public function getConfiguration(): Configuration
    {
        if ($this->_configuration === null) {
            $this->_configuration = new Configuration(['settingsManager' => $this->settings]);
            $this->_configuration->loadBySettings();
        }
        return $this->_configuration;
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/banner/config']);
    }

    /**
     * @inerhitdoc
     */
    public function getName()
    {
        return Yii::t('BannerModule.base', 'Banner');
    }

    /**
     * @inerhitdoc
     */
    public function getDescription()
    {
        return Yii::t('BannerModule.base', 'Add a customizable banner at the top of the screen');
    }
}
