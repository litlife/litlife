/*! For license information please see books.old.page.js.LICENSE.txt */
!function(t){var o={};function e(i){if(o[i])return o[i].exports;var r=o[i]={i:i,l:!1,exports:{}};return t[i].call(r.exports,r,r.exports,e),r.l=!0,r.exports}e.m=t,e.c=o,e.d=function(t,o,i){e.o(t,o)||Object.defineProperty(t,o,{enumerable:!0,get:i})},e.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},e.t=function(t,o){if(1&o&&(t=e(t)),8&o)return t;if(4&o&&"object"==typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(e.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&o&&"string"!=typeof t)for(var r in t)e.d(i,r,function(o){return t[o]}.bind(null,r));return i},e.n=function(t){var o=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(o,"a",o),o},e.o=function(t,o){return Object.prototype.hasOwnProperty.call(t,o)},e.p="/",e(e.s=41)}({41:function(t,o,e){t.exports=e("U5g1")},C9ga:function(t,o,e){"use strict";function i(t){function o(){return!1}t instanceof jQuery&&(t=t.get(0)),t.ondblclick=o,t.onselectstart=o,t.onmousedown=o}e.d(o,"a",(function(){return i}))},U5g1:function(t,o,e){"use strict";e.r(o);var i=e("C9ga"),r=e("p+/8"),a=new(e("qN0s").a);a.modal=$("#chaptersList"),a.init(),document.addEventListener("DOMContentLoaded",(function(){$(".noselect").each((function(){Object(i.a)($(this))}));var t=new r.a;t.button=$(".change_read_style").first(),t.init()}))},aI3I:function(t,o,e){"use strict";function i(){var t=this;this.init=function(){t.sidebar.length&&($(".badge-fire-if-inner-badge-primary-exists").each((function(){var o=$(this),e=o.find(".badge-primary").first(),i=t.sidebar.find(o.attr("href")+".collapse"),r=0;i.find(".list-group-item .badge").each((function(t){var o=$(this);(o.hasClass("badge-primary")||o.hasClass("badge-info"))&&(r+=parseInt($(this).text(),10))})),r>0&&e.html(r)})),t.$numberOfAllUnreadNotifications=t.$header.find(".number-of-all-unread-notifications"),t.$numberOfAllUnreadNotifications.text(t.getNumberOfAllUnreadNotifications())),t.button.unbind("click").bind("click",(function(){t.isVisible()?(t.hide(),$.ajax({method:"GET",url:"/sidebar/hide"}).done((function(t){}))):(t.show(),$.ajax({method:"GET",url:"/sidebar/show"}).done((function(t){})))}));var o=t.sidebar.find("#login_form"),e=o.find('[type="submit"]');e.bind("click",(function(){var t=e.find(".loading"),i=e.find(".enter_text");e.attr("disabled","disabled"),t.show(),i.hide(),o.submit()}))},this.show=function(){t.sidebar.addClass("d-sm-block").removeClass("d-none"),t.main.addClass("pl-260px"),t.footer.addClass("pl-260px")},this.hide=function(){t.sidebar.removeClass("d-sm-block").addClass("d-none"),t.main.removeClass("pl-260px"),t.footer.removeClass("pl-260px")},this.isVisible=function(){return t.sidebar.is(":visible")},this.getNumberOfAllUnreadNotifications=function(){var o=0;return t.sidebar.find(".count-in-toggle").each((function(){var t=$(this),e=parseInt(t.text());isNaN(e)||(o+=e)})),o}}e.d(o,"a",(function(){return i}))},d7Iu:function(t,o,e){var i,r;function a(t){return(a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}i=[e("xeH2")],void 0===(r=function(t){return function(t){"use strict";var o=function o(e,i,r,a,n){this.fallbackValue=r?"string"==typeof r?this.parse(r):r:null,this.fallbackFormat=a||"rgba",this.hexNumberSignPrefix=!0===n,this.value=this.fallbackValue,this.origFormat=null,this.predefinedColors=i||{},this.colors=t.extend({},o.webColors,this.predefinedColors),e&&(void 0!==e.h?this.value=e:this.setColor(String(e))),this.value||(this.value={h:0,s:0,b:0,a:1})};o.webColors={aliceblue:"f0f8ff",antiquewhite:"faebd7",aqua:"00ffff",aquamarine:"7fffd4",azure:"f0ffff",beige:"f5f5dc",bisque:"ffe4c4",black:"000000",blanchedalmond:"ffebcd",blue:"0000ff",blueviolet:"8a2be2",brown:"a52a2a",burlywood:"deb887",cadetblue:"5f9ea0",chartreuse:"7fff00",chocolate:"d2691e",coral:"ff7f50",cornflowerblue:"6495ed",cornsilk:"fff8dc",crimson:"dc143c",cyan:"00ffff",darkblue:"00008b",darkcyan:"008b8b",darkgoldenrod:"b8860b",darkgray:"a9a9a9",darkgreen:"006400",darkkhaki:"bdb76b",darkmagenta:"8b008b",darkolivegreen:"556b2f",darkorange:"ff8c00",darkorchid:"9932cc",darkred:"8b0000",darksalmon:"e9967a",darkseagreen:"8fbc8f",darkslateblue:"483d8b",darkslategray:"2f4f4f",darkturquoise:"00ced1",darkviolet:"9400d3",deeppink:"ff1493",deepskyblue:"00bfff",dimgray:"696969",dodgerblue:"1e90ff",firebrick:"b22222",floralwhite:"fffaf0",forestgreen:"228b22",fuchsia:"ff00ff",gainsboro:"dcdcdc",ghostwhite:"f8f8ff",gold:"ffd700",goldenrod:"daa520",gray:"808080",green:"008000",greenyellow:"adff2f",honeydew:"f0fff0",hotpink:"ff69b4",indianred:"cd5c5c",indigo:"4b0082",ivory:"fffff0",khaki:"f0e68c",lavender:"e6e6fa",lavenderblush:"fff0f5",lawngreen:"7cfc00",lemonchiffon:"fffacd",lightblue:"add8e6",lightcoral:"f08080",lightcyan:"e0ffff",lightgoldenrodyellow:"fafad2",lightgrey:"d3d3d3",lightgreen:"90ee90",lightpink:"ffb6c1",lightsalmon:"ffa07a",lightseagreen:"20b2aa",lightskyblue:"87cefa",lightslategray:"778899",lightsteelblue:"b0c4de",lightyellow:"ffffe0",lime:"00ff00",limegreen:"32cd32",linen:"faf0e6",magenta:"ff00ff",maroon:"800000",mediumaquamarine:"66cdaa",mediumblue:"0000cd",mediumorchid:"ba55d3",mediumpurple:"9370d8",mediumseagreen:"3cb371",mediumslateblue:"7b68ee",mediumspringgreen:"00fa9a",mediumturquoise:"48d1cc",mediumvioletred:"c71585",midnightblue:"191970",mintcream:"f5fffa",mistyrose:"ffe4e1",moccasin:"ffe4b5",navajowhite:"ffdead",navy:"000080",oldlace:"fdf5e6",olive:"808000",olivedrab:"6b8e23",orange:"ffa500",orangered:"ff4500",orchid:"da70d6",palegoldenrod:"eee8aa",palegreen:"98fb98",paleturquoise:"afeeee",palevioletred:"d87093",papayawhip:"ffefd5",peachpuff:"ffdab9",peru:"cd853f",pink:"ffc0cb",plum:"dda0dd",powderblue:"b0e0e6",purple:"800080",red:"ff0000",rosybrown:"bc8f8f",royalblue:"4169e1",saddlebrown:"8b4513",salmon:"fa8072",sandybrown:"f4a460",seagreen:"2e8b57",seashell:"fff5ee",sienna:"a0522d",silver:"c0c0c0",skyblue:"87ceeb",slateblue:"6a5acd",slategray:"708090",snow:"fffafa",springgreen:"00ff7f",steelblue:"4682b4",tan:"d2b48c",teal:"008080",thistle:"d8bfd8",tomato:"ff6347",turquoise:"40e0d0",violet:"ee82ee",wheat:"f5deb3",white:"ffffff",whitesmoke:"f5f5f5",yellow:"ffff00",yellowgreen:"9acd32",transparent:"transparent"},o.prototype={constructor:o,colors:{},predefinedColors:{},getValue:function(){return this.value},setValue:function(t){this.value=t},_sanitizeNumber:function(t){return"number"==typeof t?t:isNaN(t)||null===t||""===t||void 0===t?1:""===t?0:void 0!==t.toLowerCase?(t.match(/^\./)&&(t="0"+t),Math.ceil(100*parseFloat(t))/100):1},isTransparent:function(t){return!(!t||!("string"==typeof t||t instanceof String))&&("transparent"===(t=t.toLowerCase().trim())||t.match(/#?00000000/)||t.match(/(rgba|hsla)\(0,0,0,0?\.?0\)/))},rgbaIsTransparent:function(t){return 0===t.r&&0===t.g&&0===t.b&&0===t.a},setColor:function(t){if(t=t.toLowerCase().trim()){if(this.isTransparent(t))return this.value={h:0,s:0,b:0,a:0},!0;var o=this.parse(t);o?(this.value=this.value={h:o.h,s:o.s,b:o.b,a:o.a},this.origFormat||(this.origFormat=o.format)):this.fallbackValue&&(this.value=this.fallbackValue)}return!1},setHue:function(t){this.value.h=1-t},setSaturation:function(t){this.value.s=t},setBrightness:function(t){this.value.b=1-t},setAlpha:function(t){this.value.a=Math.round(parseInt(100*(1-t),10)/100*100)/100},toRGB:function(t,o,e,i){var r,a,n,s,l;return 0===arguments.length&&(t=this.value.h,o=this.value.s,e=this.value.b,i=this.value.a),t=(t*=360)%360/60,r=a=n=e-(l=e*o),r+=[l,s=l*(1-Math.abs(t%2-1)),0,0,s,l][t=~~t],a+=[s,l,l,s,0,0][t],n+=[0,0,s,l,l,s][t],{r:Math.round(255*r),g:Math.round(255*a),b:Math.round(255*n),a:i}},toHex:function(t,o,e,i,r){arguments.length<=1&&(o=this.value.h,e=this.value.s,i=this.value.b,r=this.value.a);var a="#",n=this.toRGB(o,e,i,r);if(this.rgbaIsTransparent(n))return"transparent";t||(a=this.hexNumberSignPrefix?"#":"");var s=a+((1<<24)+(parseInt(n.r)<<16)+(parseInt(n.g)<<8)+parseInt(n.b)).toString(16).slice(1);return s},toHSL:function(t,o,e,i){0===arguments.length&&(t=this.value.h,o=this.value.s,e=this.value.b,i=this.value.a);var r=t,a=(2-o)*e,n=o*e;return n/=a>0&&a<=1?a:2-a,a/=2,n>1&&(n=1),{h:isNaN(r)?0:r,s:isNaN(n)?0:n,l:isNaN(a)?0:a,a:isNaN(i)?0:i}},toAlias:function(t,o,e,i){var r,a=0===arguments.length?this.toHex(!0):this.toHex(!0,t,o,e,i),n="alias"===this.origFormat?a:this.toString(!1,this.origFormat);for(var s in this.colors)if((r=this.colors[s].toLowerCase().trim())===a||r===n)return s;return!1},RGBtoHSB:function(t,o,e,i){var r,a,n,s;return t/=255,o/=255,e/=255,r=((r=0==(s=(n=Math.max(t,o,e))-Math.min(t,o,e))?null:n===t?(o-e)/s:n===o?(e-t)/s+2:(t-o)/s+4)+360)%6*60/360,a=0===s?0:s/n,{h:this._sanitizeNumber(r),s:a,b:n,a:this._sanitizeNumber(i)}},HueToRGB:function(t,o,e){return e<0?e+=1:e>1&&(e-=1),6*e<1?t+(o-t)*e*6:2*e<1?o:3*e<2?t+(o-t)*(2/3-e)*6:t},HSLtoRGB:function(t,o,e,i){var r;o<0&&(o=0);var a=2*e-(r=e<=.5?e*(1+o):e+o-e*o),n=t+1/3,s=t,l=t-1/3;return[Math.round(255*this.HueToRGB(a,r,n)),Math.round(255*this.HueToRGB(a,r,s)),Math.round(255*this.HueToRGB(a,r,l)),this._sanitizeNumber(i)]},parse:function(o){if("string"!=typeof o)return this.fallbackValue;if(0===arguments.length)return!1;var e,i,r=this,a=!1,n=void 0!==this.colors[o];return n&&(o=this.colors[o].toLowerCase().trim()),t.each(this.stringParsers,(function(t,s){var l=s.re.exec(o);return!(e=l&&s.parse.apply(r,[l]))||(a={},i=n?"alias":s.format?s.format:r.getValidFallbackFormat(),(a=i.match(/hsla?/)?r.RGBtoHSB.apply(r,r.HSLtoRGB.apply(r,e)):r.RGBtoHSB.apply(r,e))instanceof Object&&(a.format=i),!1)})),a},getValidFallbackFormat:function(){var t=["rgba","rgb","hex","hsla","hsl"];return this.origFormat&&-1!==t.indexOf(this.origFormat)?this.origFormat:this.fallbackFormat&&-1!==t.indexOf(this.fallbackFormat)?this.fallbackFormat:"rgba"},toString:function(t,e,i){i=i||!1;var r=!1;switch(e=e||this.origFormat||this.fallbackFormat){case"rgb":return r=this.toRGB(),this.rgbaIsTransparent(r)?"transparent":"rgb("+r.r+","+r.g+","+r.b+")";case"rgba":return"rgba("+(r=this.toRGB()).r+","+r.g+","+r.b+","+r.a+")";case"hsl":return r=this.toHSL(),"hsl("+Math.round(360*r.h)+","+Math.round(100*r.s)+"%,"+Math.round(100*r.l)+"%)";case"hsla":return r=this.toHSL(),"hsla("+Math.round(360*r.h)+","+Math.round(100*r.s)+"%,"+Math.round(100*r.l)+"%,"+r.a+")";case"hex":return this.toHex(t);case"alias":return!1===(r=this.toAlias())?this.toString(t,this.getValidFallbackFormat()):i&&!(r in o.webColors)&&r in this.predefinedColors?this.predefinedColors[r]:r;default:return r}},stringParsers:[{re:/rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*?\)/,format:"rgb",parse:function(t){return[t[1],t[2],t[3],1]}},{re:/rgb\(\s*(\d*(?:\.\d+)?)\%\s*,\s*(\d*(?:\.\d+)?)\%\s*,\s*(\d*(?:\.\d+)?)\%\s*?\)/,format:"rgb",parse:function(t){return[2.55*t[1],2.55*t[2],2.55*t[3],1]}},{re:/rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d*(?:\.\d+)?)\s*)?\)/,format:"rgba",parse:function(t){return[t[1],t[2],t[3],t[4]]}},{re:/rgba\(\s*(\d*(?:\.\d+)?)\%\s*,\s*(\d*(?:\.\d+)?)\%\s*,\s*(\d*(?:\.\d+)?)\%\s*(?:,\s*(\d*(?:\.\d+)?)\s*)?\)/,format:"rgba",parse:function(t){return[2.55*t[1],2.55*t[2],2.55*t[3],t[4]]}},{re:/hsl\(\s*(\d*(?:\.\d+)?)\s*,\s*(\d*(?:\.\d+)?)\%\s*,\s*(\d*(?:\.\d+)?)\%\s*?\)/,format:"hsl",parse:function(t){return[t[1]/360,t[2]/100,t[3]/100,t[4]]}},{re:/hsla\(\s*(\d*(?:\.\d+)?)\s*,\s*(\d*(?:\.\d+)?)\%\s*,\s*(\d*(?:\.\d+)?)\%\s*(?:,\s*(\d*(?:\.\d+)?)\s*)?\)/,format:"hsla",parse:function(t){return[t[1]/360,t[2]/100,t[3]/100,t[4]]}},{re:/#?([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/,format:"hex",parse:function(t){return[parseInt(t[1],16),parseInt(t[2],16),parseInt(t[3],16),1]}},{re:/#?([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/,format:"hex",parse:function(t){return[parseInt(t[1]+t[1],16),parseInt(t[2]+t[2],16),parseInt(t[3]+t[3],16),1]}}],colorNameToHex:function(t){return void 0!==this.colors[t.toLowerCase()]&&this.colors[t.toLowerCase()]}};var e={horizontal:!1,inline:!1,color:!1,format:!1,input:"input",container:!1,component:".add-on, .input-group-addon",fallbackColor:!1,fallbackFormat:"hex",hexNumberSignPrefix:!0,sliders:{saturation:{maxLeft:100,maxTop:100,callLeft:"setSaturation",callTop:"setBrightness"},hue:{maxLeft:0,maxTop:100,callLeft:!1,callTop:"setHue"},alpha:{maxLeft:0,maxTop:100,callLeft:!1,callTop:"setAlpha"}},slidersHorz:{saturation:{maxLeft:100,maxTop:100,callLeft:"setSaturation",callTop:"setBrightness"},hue:{maxLeft:100,maxTop:0,callLeft:"setHue",callTop:!1},alpha:{maxLeft:100,maxTop:0,callLeft:"setAlpha",callTop:!1}},template:'<div class="colorpicker dropdown-menu"><div class="colorpicker-saturation"><i><b></b></i></div><div class="colorpicker-hue"><i></i></div><div class="colorpicker-alpha"><i></i></div><div class="colorpicker-color"><div /></div><div class="colorpicker-selectors"></div></div>',align:"right",customClass:null,colorSelectors:null},i=function(o,i){this.element=t(o).addClass("colorpicker-element"),this.options=t.extend(!0,{},e,this.element.data(),i),this.component=this.options.component,this.component=!1!==this.component&&this.element.find(this.component),this.component&&0===this.component.length&&(this.component=!1),this.container=!0===this.options.container?this.element:this.options.container,this.container=!1!==this.container&&t(this.container),this.input=this.element.is("input")?this.element:!!this.options.input&&this.element.find(this.options.input),this.input&&0===this.input.length&&(this.input=!1),this.color=this.createColor(!1!==this.options.color?this.options.color:this.getValue()),this.format=!1!==this.options.format?this.options.format:this.color.origFormat,!1!==this.options.color&&(this.updateInput(this.color),this.updateData(this.color)),this.disabled=!1;var r=this.picker=t(this.options.template);if(this.options.customClass&&r.addClass(this.options.customClass),this.options.inline?r.addClass("colorpicker-inline colorpicker-visible"):r.addClass("colorpicker-hidden"),this.options.horizontal&&r.addClass("colorpicker-horizontal"),-1===["rgba","hsla","alias"].indexOf(this.format)&&!1!==this.options.format&&"transparent"!==this.getValue()||r.addClass("colorpicker-with-alpha"),"right"===this.options.align&&r.addClass("colorpicker-right"),!0===this.options.inline&&r.addClass("colorpicker-no-arrow"),this.options.colorSelectors){var a=this,n=a.picker.find(".colorpicker-selectors");n.length>0&&(t.each(this.options.colorSelectors,(function(o,e){var i=t("<i />").addClass("colorpicker-selectors-color").css("background-color",e).data("class",o).data("alias",o);i.on("mousedown.colorpicker touchstart.colorpicker",(function(o){o.preventDefault(),a.setValue("alias"===a.format?t(this).data("alias"):t(this).css("background-color"))})),n.append(i)})),n.show().addClass("colorpicker-visible"))}r.on("mousedown.colorpicker touchstart.colorpicker",t.proxy((function(t){t.target===t.currentTarget&&t.preventDefault()}),this)),r.find(".colorpicker-saturation, .colorpicker-hue, .colorpicker-alpha").on("mousedown.colorpicker touchstart.colorpicker",t.proxy(this.mousedown,this)),r.appendTo(this.container?this.container:t("body")),!1!==this.input&&(this.input.on({"keyup.colorpicker":t.proxy(this.keyup,this)}),this.input.on({"input.colorpicker":t.proxy(this.change,this)}),!1===this.component&&this.element.on({"focus.colorpicker":t.proxy(this.show,this)}),!1===this.options.inline&&this.element.on({"focusout.colorpicker":t.proxy(this.hide,this)})),!1!==this.component&&this.component.on({"click.colorpicker":t.proxy(this.show,this)}),!1===this.input&&!1===this.component&&this.element.on({"click.colorpicker":t.proxy(this.show,this)}),!1!==this.input&&!1!==this.component&&"color"===this.input.attr("type")&&this.input.on({"click.colorpicker":t.proxy(this.show,this),"focus.colorpicker":t.proxy(this.show,this)}),this.update(),t(t.proxy((function(){this.element.trigger("create")}),this))};i.Color=o,i.prototype={constructor:i,destroy:function(){this.picker.remove(),this.element.removeData("colorpicker","color").off(".colorpicker"),!1!==this.input&&this.input.off(".colorpicker"),!1!==this.component&&this.component.off(".colorpicker"),this.element.removeClass("colorpicker-element"),this.element.trigger({type:"destroy"})},reposition:function(){if(!1!==this.options.inline||this.options.container)return!1;var t=this.container&&this.container[0]!==window.document.body?"position":"offset",o=this.component||this.element,e=o[t]();"right"===this.options.align&&(e.left-=this.picker.outerWidth()-o.outerWidth()),this.picker.css({top:e.top+o.outerHeight(),left:e.left})},show:function(o){this.isDisabled()||(this.picker.addClass("colorpicker-visible").removeClass("colorpicker-hidden"),this.reposition(),t(window).on("resize.colorpicker",t.proxy(this.reposition,this)),!o||this.hasInput()&&"color"!==this.input.attr("type")||o.stopPropagation&&o.preventDefault&&(o.stopPropagation(),o.preventDefault()),!this.component&&this.input||!1!==this.options.inline||t(window.document).on({"mousedown.colorpicker":t.proxy(this.hide,this)}),this.element.trigger({type:"showPicker",color:this.color}))},hide:function(o){if(void 0!==o&&o.target&&(t(o.currentTarget).parents(".colorpicker").length>0||t(o.target).parents(".colorpicker").length>0))return!1;this.picker.addClass("colorpicker-hidden").removeClass("colorpicker-visible"),t(window).off("resize.colorpicker",this.reposition),t(window.document).off({"mousedown.colorpicker":this.hide}),this.update(),this.element.trigger({type:"hidePicker",color:this.color})},updateData:function(t){return t=t||this.color.toString(!1,this.format),this.element.data("color",t),t},updateInput:function(t){return t=t||this.color.toString(!1,this.format),!1!==this.input&&(this.input.prop("value",t),this.input.trigger("change")),t},updatePicker:function(t){void 0!==t&&(this.color=this.createColor(t));var o=!1===this.options.horizontal?this.options.sliders:this.options.slidersHorz,e=this.picker.find("i");if(0!==e.length)return!1===this.options.horizontal?(o=this.options.sliders,e.eq(1).css("top",o.hue.maxTop*(1-this.color.value.h)).end().eq(2).css("top",o.alpha.maxTop*(1-this.color.value.a))):(o=this.options.slidersHorz,e.eq(1).css("left",o.hue.maxLeft*(1-this.color.value.h)).end().eq(2).css("left",o.alpha.maxLeft*(1-this.color.value.a))),e.eq(0).css({top:o.saturation.maxTop-this.color.value.b*o.saturation.maxTop,left:this.color.value.s*o.saturation.maxLeft}),this.picker.find(".colorpicker-saturation").css("backgroundColor",this.color.toHex(!0,this.color.value.h,1,1,1)),this.picker.find(".colorpicker-alpha").css("backgroundColor",this.color.toHex(!0)),this.picker.find(".colorpicker-color, .colorpicker-color div").css("backgroundColor",this.color.toString(!0,this.format)),t},updateComponent:function(t){var o;if(o=void 0!==t?this.createColor(t):this.color,!1!==this.component){var e=this.component.find("i").eq(0);e.length>0?e.css({backgroundColor:o.toString(!0,this.format)}):this.component.css({backgroundColor:o.toString(!0,this.format)})}return o.toString(!1,this.format)},update:function(t){var o;return!1===this.getValue(!1)&&!0!==t||(o=this.updateComponent(),this.updateInput(o),this.updateData(o),this.updatePicker()),o},setValue:function(t){this.color=this.createColor(t),this.update(!0),this.element.trigger({type:"changeColor",color:this.color,value:t})},createColor:function(t){return new o(t||null,this.options.colorSelectors,this.options.fallbackColor?this.options.fallbackColor:this.color,this.options.fallbackFormat,this.options.hexNumberSignPrefix)},getValue:function(t){var o;return t=void 0===t?this.options.fallbackColor:t,void 0!==(o=this.hasInput()?this.input.val():this.element.data("color"))&&""!==o&&null!==o||(o=t),o},hasInput:function(){return!1!==this.input},isDisabled:function(){return this.disabled},disable:function(){return this.hasInput()&&this.input.prop("disabled",!0),this.disabled=!0,this.element.trigger({type:"disable",color:this.color,value:this.getValue()}),!0},enable:function(){return this.hasInput()&&this.input.prop("disabled",!1),this.disabled=!1,this.element.trigger({type:"enable",color:this.color,value:this.getValue()}),!0},currentSlider:null,mousePointer:{left:0,top:0},mousedown:function(o){!o.pageX&&!o.pageY&&o.originalEvent&&o.originalEvent.touches&&(o.pageX=o.originalEvent.touches[0].pageX,o.pageY=o.originalEvent.touches[0].pageY),o.stopPropagation(),o.preventDefault();var e=t(o.target).closest("div"),i=this.options.horizontal?this.options.slidersHorz:this.options.sliders;if(!e.is(".colorpicker")){if(e.is(".colorpicker-saturation"))this.currentSlider=t.extend({},i.saturation);else if(e.is(".colorpicker-hue"))this.currentSlider=t.extend({},i.hue);else{if(!e.is(".colorpicker-alpha"))return!1;this.currentSlider=t.extend({},i.alpha)}var r=e.offset();this.currentSlider.guide=e.find("i")[0].style,this.currentSlider.left=o.pageX-r.left,this.currentSlider.top=o.pageY-r.top,this.mousePointer={left:o.pageX,top:o.pageY},t(window.document).on({"mousemove.colorpicker":t.proxy(this.mousemove,this),"touchmove.colorpicker":t.proxy(this.mousemove,this),"mouseup.colorpicker":t.proxy(this.mouseup,this),"touchend.colorpicker":t.proxy(this.mouseup,this)}).trigger("mousemove")}return!1},mousemove:function(t){!t.pageX&&!t.pageY&&t.originalEvent&&t.originalEvent.touches&&(t.pageX=t.originalEvent.touches[0].pageX,t.pageY=t.originalEvent.touches[0].pageY),t.stopPropagation(),t.preventDefault();var o=Math.max(0,Math.min(this.currentSlider.maxLeft,this.currentSlider.left+((t.pageX||this.mousePointer.left)-this.mousePointer.left))),e=Math.max(0,Math.min(this.currentSlider.maxTop,this.currentSlider.top+((t.pageY||this.mousePointer.top)-this.mousePointer.top)));return this.currentSlider.guide.left=o+"px",this.currentSlider.guide.top=e+"px",this.currentSlider.callLeft&&this.color[this.currentSlider.callLeft].call(this.color,o/this.currentSlider.maxLeft),this.currentSlider.callTop&&this.color[this.currentSlider.callTop].call(this.color,e/this.currentSlider.maxTop),!1!==this.options.format||"setAlpha"!==this.currentSlider.callTop&&"setAlpha"!==this.currentSlider.callLeft||(1!==this.color.value.a?(this.format="rgba",this.color.origFormat="rgba"):(this.format="hex",this.color.origFormat="hex")),this.update(!0),this.element.trigger({type:"changeColor",color:this.color}),!1},mouseup:function(o){return o.stopPropagation(),o.preventDefault(),t(window.document).off({"mousemove.colorpicker":this.mousemove,"touchmove.colorpicker":this.mousemove,"mouseup.colorpicker":this.mouseup,"touchend.colorpicker":this.mouseup}),!1},change:function(t){this.color=this.createColor(this.input.val()),this.color.origFormat&&!1===this.options.format&&(this.format=this.color.origFormat),!1!==this.getValue(!1)&&(this.updateData(),this.updateComponent(),this.updatePicker()),this.element.trigger({type:"changeColor",color:this.color,value:this.input.val()})},keyup:function(t){38===t.keyCode?(this.color.value.a<1&&(this.color.value.a=Math.round(100*(this.color.value.a+.01))/100),this.update(!0)):40===t.keyCode&&(this.color.value.a>0&&(this.color.value.a=Math.round(100*(this.color.value.a-.01))/100),this.update(!0)),this.element.trigger({type:"changeColor",color:this.color,value:this.input.val()})}},t.colorpicker=i,t.fn.colorpicker=function(o){var e=Array.prototype.slice.call(arguments,1),r=1===this.length,n=null,s=this.each((function(){var r=t(this),s=r.data("colorpicker"),l="object"===a(o)?o:{};s||(s=new i(this,l),r.data("colorpicker",s)),"string"==typeof o?t.isFunction(s[o])?n=s[o].apply(s,e):(e.length&&(s[o]=e[0]),n=s[o]):n=r}));return r?n:s},t.fn.colorpicker.constructor=i}(t)}.apply(o,i))||(t.exports=r)},mpcQ:function(t,o,e){"use strict";e.d(o,"a",(function(){return r}));var i=e("aI3I");function r(){var t=this;t.options={format:"hex",inline:!1,container:!0,customClass:"colorpicker-2x",sliders:{saturation:{maxLeft:200,maxTop:200},hue:{maxTop:200},alpha:{maxTop:200}}},this.init=function(){t.sidebar=new i.a,t.sidebar.sidebar=$("#sidebar"),t.sidebar.main=$("#main"),t.sidebar.footer=$("#footer"),t.sidebar.button=$("[data-target='#sidebar']"),t.book_text=$(".book_text"),t.card=$(".card"),t.body=$("body"),t.background_color_colorpicker=t.form.find("#cp1").colorpicker(t.options),t.font_color_colorpicker=t.form.find("#cp2").colorpicker(t.options),t.card_color_colorpicker=t.form.find("#cp3").colorpicker(t.options),t.form=$("form.read_style").first(),t.cssToValues(),t.resetButton.unbind("click").on("click",t.onResetClick),t.form.submit(t.onFormSubmit),t.form.find("[name=font]").change((function(){t.valuesToCss()})),t.form.find("[name=align]").change((function(){t.valuesToCss()})),t.form.find("[name=size]").change((function(){t.valuesToCss()})),t.form.find("[name=background_color]").change((function(){t.valuesToCss()})),t.form.find("[name=card_color]").change((function(){t.valuesToCss()})),t.form.find("[name=font_color]").change((function(){t.valuesToCss()})),t.form.find("[name=show_sidebar]").change((function(){$(this).prop("checked")?$(this).prop("checked",!0):$(this).prop("checked",!1),t.valuesToCss()}))},this.onFormSubmit=function(o){t.book_text.length>0&&(o.preventDefault(),$.ajax({method:"POST",url:t.form.attr("action"),data:t.form.serializeArray()}).done(t.onLoadDone))},this.onLoadDone=function(o){t.form.find(".output").html(o)},this.onResetClick=function(){t.form.find("[name=font]").val($("#font").data("default")),t.form.find("[name=align]").val($("#align").data("default")),t.form.find("[name=size]").val($("#size").data("default")),t.form.find("[name=background_color]").val($("#background_color").data("default")),t.form.find("[name=card_color]").val($("#card_color").data("default")),t.form.find("[name=font_color]").val($("#font_color").data("default"));var o=t.form.find('[name=show_sidebar][type="checkbox"]').first();o.data("default")?o.prop("checked",!0):o.prop("checked",!1),t.valuesToCss(),t.background_color_colorpicker.colorpicker("setValue",$("#background_color").data("default")),t.card_color_colorpicker.colorpicker("setValue",$("#card_color").data("default")),t.font_color_colorpicker.colorpicker("setValue",$("#font_color").data("default")),t.sidebarToggle()},this.valuesToCss=function(){if(t.book_text.length>0){var o=t.form.find("[name=font]").val();"Default"===o?t.book_text.css("font-family","inherit"):t.book_text.css("font-family",o),t.book_text.css("text-align",t.form.find("[name=align]").val()),t.book_text.css("font-size",t.form.find("[name=size]").val()+"px"),t.body.css("background-color",t.form.find("[name=background_color]").val()),t.card.css("background-color",t.form.find("[name=card_color]").val()),t.book_text.css("color",t.form.find("[name=font_color]").val()),t.sidebarToggle()}},this.cssToValues=function(){t.book_text.length>0&&(t.form.find("[name=font]").val($.trim(t.book_text.css("font-family")).replace(/([\"\']*)/g,"")),t.form.find("[name=align]").val(t.book_text.css("text-align")),t.form.find("[name=size]").val(parseInt(t.book_text.css("font-size"))),t.background_color_colorpicker.colorpicker("setValue",t.body.css("background-color")),t.card_color_colorpicker.colorpicker("setValue",t.card.css("background-color")),t.font_color_colorpicker.colorpicker("setValue",t.book_text.css("color")),t.sidebarToggle())},this.sidebarToggle=function(){t.form.find('[name=show_sidebar][type="checkbox"]').prop("checked")?t.sidebar.show():t.sidebar.hide()}}e("d7Iu")},"p+/8":function(t,o,e){"use strict";e.d(o,"a",(function(){return r}));var i=e("mpcQ");function r(){var t=this;this.init=function(){t.button.unbind("click").on("click",(function(o){o.preventDefault(),t.button.addClass("loading-cap"),t.modal=bootbox.dialog({message:'<div class="text-center"><h1><i class="fas fa-spinner fa-spin"></i></h1></div>',backdrop:!1,size:"small"}),t.modal.init((function(){$.ajax({method:"GET",url:"/read_style"}).done(t.onLoadDone).fail(t.onLoadFail)}))}))},this.onLoadDone=function(o){t.button.removeClass("loading-cap"),t.modal.find(".bootbox-body").html(o);var e=new i.a;e.form=$("form.read_style"),e.resetButton=e.form.find(".reset").first(),e.init()},this.onLoadFail=function(o){t.modal.modal("hide"),t.button.removeClass("loading-cap")}}},qN0s:function(t,o,e){"use strict";function i(){var t=this;this.init=function(){t.modalBody=t.modal.find(".modal-body"),t.modalBody.html('<div class="text-center"><i class="h1 fas fa-spinner fa-spin"></i></div>'),t.modal.on("show.bs.modal",t.showBsModal)},this.showBsModal=function(o){t.modalBody.find(".list-group").length<1&&$.ajax({method:"GET",url:t.modal.data("href")}).done(t.onLoadDone)},this.onLoadDone=function(o){t.modalBody.html(o)}}e.d(o,"a",(function(){return i}))},xeH2:function(t,o){t.exports=jQuery}});