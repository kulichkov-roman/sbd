$(function () {
    window.openKvkForm = function () {
        console.log('KVK in not inited yet');
    };

    if (typeof jQuery !== 'undefined') {
        $('body').on('click', '.js-kvk-button', function(e) {
            e.preventDefault();
            window.openKvkForm($(this).data('kvk'));
        });
    }

    window.onKvkload = function(KVK) {
        // if (typeof __KVKData === 'undefined') {
        //     console.log('No __KVKData variable');
        //     return;
        // }
        window.openKvkForm = function (data) {
            console.log('Opening KVK form', data);
            var form = KVK.ui('form', data);
            form.open();
        };
        console.log('KVK is loaded');
    }

    function loadKvkLib(path, fnName) {
        var scriptPath, firstScript, scriptElement;

        scriptPath = path + '?onload=' + fnName;

        firstScript   = document.getElementsByTagName('script')[0];
        scriptElement = document.createElement('script');
        scriptElement.src = scriptPath;
        firstScript.parentNode.insertBefore(scriptElement, firstScript.nextSibling || firstScript);
    }

    loadKvkLib('https://form-test.kupivkredit.ru/sdk/v1/sdk.js', 'onKvkload');
});
