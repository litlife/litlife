!function(n){var e={};function t(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return n[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}t.m=n,t.c=e,t.d=function(n,e,r){t.o(n,e)||Object.defineProperty(n,e,{enumerable:!0,get:r})},t.r=function(n){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(n,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(n,"__esModule",{value:!0})},t.t=function(n,e){if(1&e&&(n=t(n)),8&e)return n;if(4&e&&"object"==typeof n&&n&&n.__esModule)return n;var r=Object.create(null);if(t.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:n}),2&e&&"string"!=typeof n)for(var o in n)t.d(r,o,function(e){return n[e]}.bind(null,o));return r},t.n=function(n){var e=n&&n.__esModule?function(){return n.default}:function(){return n};return t.d(e,"a",e),e},t.o=function(n,e){return Object.prototype.hasOwnProperty.call(n,e)},t.p="/",t(t.s=26)}({26:function(n,e,t){n.exports=t("Iy0o")},Iy0o:function(n,e,t){"use strict";t.r(e);var r=t("Jwu9");$(".item").each((function(){Object(r.a)($(this))}))},Jwu9:function(n,e,t){"use strict";function r(n){n.data("forum-id");var e=n.data("id"),t=n.find(".delete");t.unbind("click").on("click",(function(){o($(this))}));var r=n.find(".restore");function o(o){o.button("loading"),o.hide(),$.ajax({method:"DELETE",url:"/topics/"+e}).done((function(e){e.deleted_at?(t.hide(),r.show(),n.find(".name").addClass("transparency"),n.find(".description").addClass("transparency")):(t.show(),r.hide(),n.find(".name").removeClass("transparency"),n.find(".description").removeClass("transparency")),r.button("reset"),t.button("reset")})).fail((function(){o.show()}))}r.unbind("click").on("click",(function(){o($(this))}))}t.d(e,"a",(function(){return r}))}});