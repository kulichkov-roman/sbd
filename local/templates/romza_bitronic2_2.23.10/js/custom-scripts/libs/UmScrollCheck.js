// Checks window scrollTop against target and toggles specified class on el
// opt.check : check window top or window bottom?
// opt.class : class to toggle
// opt.onCross : function that is called on crossing target
// opt.offset : limit offset from el.offset().top
function UmScrollCheck(el, opt){
	var win = $(window);
	var t = this;
	t.check = ( opt ) ? (opt.check || 'top' ) : 'top';
	t.class = ( opt ) ? ( opt.class || 'shown' ) : 'shown';
	t.onCross = ( opt ) ? ( opt.onCross || function(){} ) : function(){};
	t.offset = ( opt ) ? ( opt.offset || 0 ) : 0;
	
	var target = el.offset().top + t.offset;
	var curPos, state;

	t.update = function(){
		curPos = ( t.check === 'top' ) ? win.scrollTop() : win.scrollTop() + win.height();
		if ( curPos < target && state !== 'before' ){
			el.removeClass(t.class);
			state = 'before';
			t.onCross(state);
		} else if ( curPos >= target && state !== 'after' ){
			el.addClass(t.class);
			state = 'after';
			t.onCross(state);
		}
	}
	t.update();

	v = globalCounter++;
	win.on('scroll.umScrollCheck'+v, t.update);

	var updating = setInterval(function(){
		target = el.offset().top + t.offset;
		t.update();
	}, 2000);

	t.destroy = function(){
		win.off('scroll.umScrollCheck'+v);
		el.removeClass(t.class);
		state = null;
		clearInterval(updating);
	}
}