!function(e){var t={};function n(i){if(t[i])return t[i].exports;var o=t[i]={i:i,l:!1,exports:{}};return e[i].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,i){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(i,o,function(t){return e[t]}.bind(null,o));return i},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/",n(n.s=38)}({38:function(e,t,n){e.exports=n("AH8o")},AH8o:function(e,t,n){"use strict";n.r(t);var i=n("pz7U");$(".manager").each((function(){Object(i.a)($(this))}))},pz7U:function(e,t,n){"use strict";function i(){var e=this;this.init=function(t){e.i=t,e.id=e.i.data("manager-id"),e.btn_approve=e.i.find(".btn-approve"),e.btn_decline=e.i.find(".btn-decline"),e.start_review=e.i.find(".btn-start-review"),e.stop_review=e.i.find(".btn-stop-review"),e.btn_delete=e.i.find(".btn-delete"),e.status=e.i.find(".status"),e.btn_approve.unbind("click").on("click",e.onApprove),e.btn_decline.unbind("click").on("click",e.onDecline),e.start_review.unbind("click").on("click",e.onStartReview),e.stop_review.unbind("click").on("click",e.onStopReview)},this.onApprove=function(t){var n=$(this);t.preventDefault(),n.addClass("loading-cap"),$.ajax({method:"GET",url:"/managers/"+e.id+"/approve/"}).done((function(t){n.removeClass("loading-cap"),e.status.html(t),e.btn_approve.hide(),e.btn_decline.hide(),e.start_review.hide(),e.stop_review.hide()})).fail((function(){n.removeClass("loading-cap")}))},this.onDecline=function(t){var n=$(this);t.preventDefault(),n.addClass("loading-cap"),$.ajax({method:"GET",url:"/managers/"+e.id+"/decline/"}).done((function(t){n.removeClass("loading-cap"),e.status.html(t),e.btn_approve.hide(),e.btn_decline.hide(),e.start_review.hide(),e.stop_review.hide()})).fail((function(){n.removeClass("loading-cap")}))},this.onStartReview=function(t){var n=$(this);t.preventDefault(),n.addClass("loading-cap"),$.ajax({method:"GET",url:"/managers/"+e.id+"/start_review/"}).done((function(t){n.removeClass("loading-cap"),e.status.html(t),e.btn_approve.show(),e.btn_decline.show(),e.start_review.hide(),e.stop_review.show()})).fail((function(){n.removeClass("loading-cap")}))},this.onStopReview=function(t){var n=$(this);t.preventDefault(),n.addClass("loading-cap"),$.ajax({method:"GET",url:"/managers/"+e.id+"/stop_review/"}).done((function(t){n.removeClass("loading-cap"),e.status.html(t),e.btn_approve.hide(),e.btn_decline.hide(),e.start_review.show(),e.stop_review.hide()})).fail((function(){n.removeClass("loading-cap")}))}}n.d(t,"a",(function(){return i}))}});