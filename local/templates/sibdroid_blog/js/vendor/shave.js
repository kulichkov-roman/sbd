!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?t():"function"==typeof define&&define.amd?define(t):t()}(0,function(){"use strict";if("undefined"!=typeof window){var e=window.$||window.jQuery||window.Zepto;e&&(e.fn.shave=function(e,t){return function(e,t){var n=2<arguments.length&&void 0!==arguments[2]?arguments[2]:{};if(!t)throw Error("maxHeight is required");var i="string"==typeof e?document.querySelectorAll(e):e;if(i){var o=n.character||"…",a=n.classname||"js-shave",s="boolean"!=typeof n.spaces||n.spaces,r='<span class="js-shave-char">'.concat(o,"</span>");"length"in i||(i=[i]);for(var c=0;c<i.length;c+=1){var f=i[c],h=f.style,l=f.querySelector(".".concat(a)),d=void 0===f.textContent?"innerText":"textContent";l&&(f.removeChild(f.querySelector(".js-shave-char")),f[d]=f[d]);var v=f[d],g=s?v.split(" "):v;if(!(g.length<2)){var u=h.height;h.height="auto";var p=h.maxHeight;if(h.maxHeight="none",f.offsetHeight<=t)h.height=u,h.maxHeight=p;else{for(var y=g.length-1,j=0,m=void 0;j<y;)m=j+y+1>>1,f[d]=s?g.slice(0,m).join(" "):g.slice(0,m),f.insertAdjacentHTML("beforeend",r),f.offsetHeight>t?y=s?m-1:m-2:j=m;f[d]=s?g.slice(0,y).join(" "):g.slice(0,y),f.insertAdjacentHTML("beforeend",r);var H=s?" ".concat(g.slice(y).join(" ")):g.slice(y);f.insertAdjacentHTML("beforeend",'<span class="'.concat(a,'" style="display:none;">').concat(H,"</span>")),h.height=u,h.maxHeight=p}}}}}(this,e,t),this})}});