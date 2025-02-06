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
 */

use humhub\modules\ui\view\components\View;

?>

<div id="banner">
    <?php if ($closeButton): ?>
        <button id="banner-close" type="button" class="close">×</button>
    <?php endif; ?>
    <?= $content ?>
</div>

<script >
    $(document).ready(function () {
        $('#banner').fadeIn(1000);
    });
</script>
