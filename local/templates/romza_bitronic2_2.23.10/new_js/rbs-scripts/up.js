(function(){

    var isAnimate = false;

    var htmlNode = document.querySelector('html');
    var classButton = 'up' + (htmlNode.classList.contains('webp') ? '-webp-' : '-png-') + ($(window).width() < 767 ? 'xs' : 'md');
    var upNode = document.createElement("DIV");
    
    upNode.classList.add('to-up-arrow', 'hide', classButton);
    document.querySelector('body').appendChild(upNode);

    setTimeout(function(){
        var st = window.pageYOffset || document.documentElement.scrollTop;
        if(document.documentElement.clientHeight < st){
            upNode.classList.remove('hide');
            return;
        }
    }, 100);

    function backToTop() {
        var st = window.pageYOffset || document.documentElement.scrollTop;
        if (st > 0) {
            isAnimate = true;
            $('html, body').animate({
                scrollTop: $(".rbs-mask").offset().top
            }, 200, function(){
                isAnimate = false;
            });
            upNode.classList.add('hide');
        }
    }

    upNode.addEventListener('click', backToTop);

    var lastScrollTop = 0;
    document.addEventListener('scroll', function(){

        if(isAnimate){
            return;
        }

        var st = window.pageYOffset || document.documentElement.scrollTop;

        if(document.documentElement.clientHeight > st){
            upNode.classList.add('hide');
            return;
        }

        upNode.classList.remove('hide');

        if (st > lastScrollTop){
            upNode.classList.remove('upper');
        } else {
            upNode.classList.add('upper');
        }
        lastScrollTop = st <= 0 ? 0 : st;

    }, {passive: true});
})(); 