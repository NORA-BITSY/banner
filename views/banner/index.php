<?php
/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

use humhub\libs\Html;
use humhub\modules\banner\assets\BannerAssets;
use humhub\modules\banner\models\Configuration;
use humhub\modules\ui\view\components\View;
use humhub\modules\banner\models\BannerType;

/**
 * @var $this View
 * @var $configuration Configuration
 */

$id = 'banner';
$close = $configuration->closeButton ? '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' : '';
if ($configuration->bannerType === BannerType::MANUAL) {
    $content = Yii::$app->user->isGuest ? $configuration->contentGuests : $configuration->content;
} else {
    $content = $this->render('types/' . $configuration->bannerType);
}
$style = $configuration->style;
?>

<?php if (!empty(trim($content))): ?>
<div id="banner" class="alert alert-<?= Html::encode($style) ?>" role="alert">
    <?php if ($closeButton): ?>
        <button id="banner-close" type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    <?php endif; ?>
    <div id="banner-container">
        <div id="banner-content">
            <?= $content ?>
        </div>
    </div>
</div>

<script <?= Html::nonce() ?>>
    $(function () {
        <?php if ($closeButton): ?>
        $('#banner-close').on('click', function () {
            $('#banner').hide();
            $(':root').css('--hh-banner-height', '0px');
        });
        <?php endif; ?>

        // Add the alert-dismissible class to the banner if a close button is present
        if ($('#banner-close').length) {
            $('#banner').addClass('alert-dismissible');
        }
    });
</script>
<?php endif; ?>
