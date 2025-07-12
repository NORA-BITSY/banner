<?php
/**
 * Banner
 * @link https://www.cuzy.app
 * @license https://www.cuzy.app/cuzy-license
 * @author [Marc FARRE](https://marc.fun)
 */

/**
 * @var $view View
 * @var $content string
 * @var $closeButton bool
 * @var $style string
 */

use humhub\libs\Html;
use humhub\modules\ui\view\components\View;

?>

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
