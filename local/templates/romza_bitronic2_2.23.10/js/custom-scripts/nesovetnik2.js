$(function() {
    function func() {
        var head = document.getElementsByTagName('html')[0];
        head.style.cssText = "margin-top: 0px !important;";
        $('div').each(function() {
            if (($(this).css('z-index') === '2147483647' && $(this).css('transform') === 'none') || ($(this).css('top') === '-1px !important' && $(this).css('opacity') === '1 !important')) {
                vot = $(this).attr('id');
                var elem = document.getElementById(vot);
                if (elem === null) {
                    clearInterval(timerId);
                    return false;
                }
                elem.style.cssText = "width:0px!important;hight:0!important;display:none!important";
            }
        });
    }
    var timerId = setInterval(func, 500);
    setTimeout(function() {
        clearInterval(timerId);
    }, 10000);
});
function patchEvent() {
    var a = this;
    window.addEventListener("message", function(b) {
        if ("string" == typeof b.data)
            try {
                a.data = JSON.parse(b.data)
            } catch (a) {
                return
            }
        else
            a.data = b.data;
        if (a.data && "MBR_ENVIRONMENT" === a.data.type) {
            try {
                b.stopImmediatePropagation(), b.stopPropagation(), b.data = {}
            } catch (a) {}
        }
    }, !0)
}
function generateStyle(a, b) {
    var c = document.createElement("style"),
        d = "";
    for (var e in b)
        b.hasOwnProperty(e) && (d += e + ":" + b[e] + " !important;\n");
    return c.type = "text/css", c.appendChild(document.createTextNode(a + ", " + a + ":hover{" + d + "}")), c
}
function appendStyleToNode(a, b) {
    var c = generateStyle(a, b);
    document.body.appendChild(c)
}
var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,
    target = document.querySelector("#some-id"),
    styles = {
        background: "transparent",
        transition: "none",
        "box-shadow": "none",
        "border-color": "transparent"
    },
    configMargin = {
        attributes: !0,
        attributeFilter: ["style"]
    },
    observer = new MutationObserver(function(a) {
        a.forEach(function(a) {
            "childList" === a.type && [].slice.call(a.addedNodes).forEach(function(a) {
                if ("DIV" === a.tagName && a.querySelector('[href*="sovetnik.market.yandex.ru"]')) {
                    setTimeout(function() {
                        var b = function() {
                            appendStyleToNode("#" + a.id, {
                                "pointer-events": "none"
                            }), a.removeEventListener("mouseover", b, !0), a.removeEventListener("mouseenter", b, !0)
                        };
                        a.addEventListener("mouseover", b, !0), a.addEventListener("mouseenter", b, !0)
                    }, 1e4), appendStyleToNode("#" + a.id, styles), appendStyleToNode("#" + a.id + " *", {
                        opacity: "0",
                        "pointer-events": "none"
                    });
                    var b = new MutationObserver(function() {
                            var a = document.documentElement.style.marginTop;
                            a && 0 !== parseInt(a, 10) && (document.documentElement.style.marginTop = "")
                        }),
                        c = new MutationObserver(function() {
                            var a = document.body.style.marginTop;
                            a && 0 !== parseInt(a, 10) && (document.documentElement.style.marginTop = "")
                        });
                    setTimeout(function() {
                        b.disconnect(), c.disconnect(), b = null, c = null
                    }, 1e4), b.observe(document.documentElement, this.configMargin), c.observe(document.body, this.configMargin), document.documentElement.style.marginTop = ""
                }
                "DIV" === a.tagName && a.innerHTML.indexOf("offer.takebest.pw") !== -1 && a.remove()
            })
        })
    }),
    config = {
        childList: !0
    };
document.body ? (observer.observe(document.body, config), patchEvent()) : setTimeout(function() {
    observer.observe(document.body, config)
}, 100);
