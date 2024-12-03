humhub.module('banner', function (module, require, $) {
    module.initOnPjaxLoad = false;

    /**
     * @param isPjax
     */
    const init = function (isPjax) {
        if (!isPjax) {
            $(function () {
                loadBanner();
            });
        }
    };

    const loadBanner = function () {
        if ($('#banner').length) {
            return;
        }

        $.ajax({
            url: module.config.contentUrl,
            dataType: 'html'
        })
            .done(function (data) {
                $('body').prepend(data);
            });
    };

    module.export({
        init: init
    });
});
