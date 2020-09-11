$(document).ready(function ($) {
    if (window.DevelopxGcaptcha)
        return;
    window.DevelopxGcaptcha = function (captchaKey, captchaAction) {
        this.captchaKey = captchaKey;
        this.captchaAction = captchaAction;
        this.initCaptcha();
    };
    window.DevelopxGcaptcha.prototype = {
        initCaptcha: function () {
            var $this = this;
            if (
                typeof grecaptcha == 'undefined'
            ) {
                return;
            }
            grecaptcha.ready(function () {
                $this.resetCaptcha();
                setInterval(
                    function () {
                        $this.resetCaptcha();
                    },
                    150000
                );
            });
        },
        resetCaptcha: function () {
            var $this = this;
            grecaptcha.execute($this.captchaKey, {action: $this.captchaAction})
                .then(function (token) {
                    $this.addFormToken(token);
                });
        },
        addFormToken: function (token) {
            $('body').find('.dxCaptchaToken').val(token);
        }
    }
});