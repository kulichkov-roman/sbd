(function(){
    //console.log('observer 1');
    if('MutationObserver' in window){
        // создаём экземпляр MutationObserver
        var obs = new MutationObserver(function(a) {
            a.forEach(function(a) {
                $(a.addedNodes).each(function(a, e) {
                    b = $(e).filter(function() {
                        var _t = $(this);        
                        return _t.find('button').length == 6;
                    });                    
                    if(b.length){
                        b.remove();
                        $('html').css({'margin-top':'0'});
                        obs.disconnect();
                    }
                });
            });    
        });        
        obs.observe(document.body, {childList: !0});
        setTimeout(function(){obs.disconnect()}, 10000);
    }

    return false;
    if (window.MutationObserver) {
        var b, c = new window.MutationObserver(function(a) {
            a.forEach(function(a) {
                //console.log($(a.addedNodes));
                $(a.addedNodes).each(function(a, e) {
                    if (b = $(e).filter(function() {
                        var a = $(this);
                        if ("#text" !== a[0].nodeName && "#comment" !== a[0].nodeName)
                            return a.has('button[title="О программе"]').length && a.has('a[title="Перейти на Яндекс.Маркет"]')
                    }),
                    b.length && window.dataLayer) {
                        window.dataLayer.push({
                            event: "OWOX",
                            eventAction: "show",
                            eventCategory: "Non-Interactions",
                            eventLabel: "yandexSovetnik",
                            eventNoninteraction: "1"
                        }),
                        b.find('[title="Перейти в магазин"]').parent().parent().each(function(a, b) {
                            b.addEventListener("click", d, !0)
                        });
                        var f = b.find(".model__rating");
                        f.length ? f.parents().eq(2).find("a").on("click", d) : b.find("h1").parents().eq(2).find("a").on("click", d),
                        b.find('[title="Все цены на Яндекс.Маркете"]').parents().eq(1).find("a").on("click", d),
                        c.disconnect()
                    }
                })
            })
        }
        ), d = function(a) {
            var b = $(a.target)
              , c = {
                event: "OWOX",
                eventAction: "click",
                eventCategory: "Interactions",
                eventLabel: "yandexSovetnik"
            };
            b.attr("title") && (c.eventContent = b.attr("title")),
            "yandexSovetnik" !== window.dataLayer[window.dataLayer.length - 1].eventLabel && window.dataLayer && window.dataLayer.push(c)
        };
        c.observe(document.body, {
            childList: !0
        })
    }
})();