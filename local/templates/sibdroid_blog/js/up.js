(function(){
    var upNode = document.createElement("DIV");
    upNode.classList.add('to-up-arrow');
    upNode.classList.add('hide');
    document.querySelector('body').appendChild(upNode);

    function backToTop() {
        var st = window.pageYOffset || document.documentElement.scrollTop;
        if (st > 0) {
            window.scrollBy(0, -80);
            setTimeout(backToTop, 0);
        }
    }

    upNode.addEventListener('click', backToTop);

    setTimeout(function(){
        var st = window.pageYOffset || document.documentElement.scrollTop;
        if(document.documentElement.clientHeight < st){
            upNode.classList.remove('hide');
            return;
        }
    }, 100);

    var lastScrollTop = 0;
    document.addEventListener('scroll', function(){
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
    });
})();