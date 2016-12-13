(function () {
    window.openKvkForm = function () {
        console.log('KVK in not inited yet');
    };

    window.onKvkload = function(KVK) {
        if (typeof __KVKData === 'undefined') {
            console.log('No __KVKData variable');
            return;
        }
        var form = KVK.ui('form', __KVKData);
        window.openKvkForm = function () {
            form.open();
        };
        if (typeof jQuery !== 'undefined') {
            jQuery('.js-kvk-button').on('click', function() {
                openKvkForm();
            });
        }
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

    loadKvkLib('https://form.kupivkredit.ru/sdk/v1/sdk.js', 'onKvkload');
})();
