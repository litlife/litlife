!function(t){var e={};function i(n){if(e[n])return e[n].exports;var o=e[n]={i:n,l:!1,exports:{}};return t[n].call(o.exports,o,o.exports,i),o.l=!0,o.exports}i.m=t,i.c=e,i.d=function(t,e,n){i.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},i.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},i.t=function(t,e){if(1&e&&(t=i(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)i.d(n,o,function(e){return t[e]}.bind(null,o));return n},i.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return i.d(e,"a",e),e},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},i.p="/",i(i.s=12)}({12:function(t,e,i){t.exports=i("cWvU")},FzaM:function(t,e,i){"use strict";i.d(e,"a",(function(){return o}));var n=i("ZI2Y");function o(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,i=t.data("id"),s=t.data("level"),r=(t.data("commentable-type"),t.data("commentable-id"),t.find(".html_box").first()),a=t.find(".buttons-panel").first();t.descendants=t.find(".descendants").first();var c=a.find(".open_descendants").first(),d=a.find(".close_descendants").first();t.counter_expand_descendants=c.find(".counter:first"),t.counter_compress_descendants=d.find(".counter:first");var l=a.find(".btn-expand").first(),u=a.find(".btn-compress").first(),p=a.find(".delete").first(),h=a.find(".restore").first(),f=a.find(".get_link").first(),v=a.find(".comment_like").first(),m=a.find(".approve").first(),g=a.find(".btn-edit").first(),b=a.find(".btn-reply").first(),y=a.find("button.share").first(),k=a.find(".publish").first();t.data("parent-id");function x(){c.attr("disabled","disabled"),d.attr("disabled","disabled"),t.descendants.html('<div class="text-align: center; font-size:48px;"><i class="fas fa-spinner fa-spin"></i></div>'),$.ajax({method:"GET",url:"/comments/"+i+"/descendants?level="+s}).done((function(e){t.descendants.html(e),t.descendants.find(".item:first").siblings(".item").addBack().each((function(){o($(this),t)})),c.hide(),d.removeAttr("disabled"),d.show()})).always((function(){c.removeAttr("disabled")}))}function w(){t.descendants.children(".item").remove(),d.hide(),c.show()}function _(e){e.button("loading"),e.hide(),$.ajax({method:"DELETE",url:"/comments/"+i}).done((function(e){e.deleted_at?(p.hide(),h.show(),r.addClass("transparency"),null!=t.parent&&t.parent.decrement_childs()):(p.show(),h.hide(),r.removeClass("transparency"),null!=t.parent&&t.parent.increment_childs()),h.button("reset"),p.button("reset")}))}function C(){var t=v.find(".counter"),e=v.find("button.liked"),n=v.find("button.empty");v.addClass("loading-cap");var o=parseInt(t.first().text());isNaN(o)&&(o=0),e.is(":visible")?(e.hide(),n.show(),o--):(e.show(),n.hide(),o++),t.text(o),$.ajax({method:"GET",url:"/comments/"+i+"/vote/1"}).done((function(i){(o=i.vote_up)<1&&(o=""),t.text(o),o>0?t.show():t.hide(),i.vote>0?(e.show(),n.hide()):(e.hide(),n.show()),v.removeClass("loading-cap")})).fail((function(i){e.is(":visible")?(e.hide(),n.show(),t.text(o-1)):(e.show(),n.hide(),t.text(o+1)),v.removeClass("loading-cap")}))}function T(){event.preventDefault(),event.stopPropagation(),m.hide(),$.ajax({method:"GET",url:"/comments/"+i+"/approve"}).done((function(t){t.status_changed_at||$(this).show()}))}function P(t){t.preventDefault(),bootbox.alert('<textarea class="form-control">'+f.data("href")+"</textarea>")}function O(e){e.preventDefault();var i=t.descendants.children(".reply-box").first();i.length<1?(b.addClass("loading-cap"),$.ajax({method:"GET",url:b.attr("href")}).done((function(e){t.descendants.append(e);var i=t.descendants.children(".reply-box").first();$(window).scrollTo(i),b.removeClass("loading-cap");var n=i.find("form").first();set_sceditor(n.find(".sceditor").first().get(0)),n.ajaxForm({dataType:"json",beforeSend:function(t){i.addClass("loading-cap")},success:function(e){$.ajax({method:"GET",url:"/comments/"+e.id}).done((function(n){i.removeClass("loading-cap"),i.after(n),i.remove(),t.descendants.find(".item[data-id='"+e.id+"']").each((function(){o($(this),t)})),t.increment_childs()}))},error:function(t){i.removeClass("loading-cap")}})})).fail((function(t){b.removeClass("loading-cap")}))):$(window).scrollTo(i)}function L(){event.preventDefault(),g.hide(),$.ajax({method:"GET",url:g.attr("href")}).done((function(e){r.hide(),r.after(e);var i=t.self.find("form:first");set_sceditor(i.find(".sceditor").first().get(0)),$(window).scrollTo(t),i.ajaxForm({dataType:"json",beforeSend:function(t){i.addClass("loading-cap")},success:function(e){r.html(e.text),r.show(),i.remove(),o(t),$(window).scrollTo(t),g.show()},error:function(t){i.removeClass("loading-cap")}})})).fail((function(){g.show()}))}t.self=t.find("[data-self]:first"),t.parent=e,b.unbind("click").on("click",O),g.unbind("click").on("click",L),f.removeAttr("href").unbind("click").on("click",P),m.unbind("click").on("click",T),r.htmlExpand({expand_button:l,compress_button:u,onExpand:function(){},onCompress:function(){$(window).scrollTo(t)}}),(new n.a).init(y),p.unbind("click").on("click",(function(){_($(this))})),h.unbind("click").on("click",(function(){_($(this))})),c.unbind("click").on("click",x),d.unbind("click").on("click",w),v.unbind("click").on("click",(function(){C()})),t.descendants.find(".item:first").siblings(".item").addBack().each((function(){o($(this),t)})),t.increment_childs=function(){var e=t.counter_expand_descendants.text();e=parseInt(e,10)+1,t.counter_expand_descendants.text(e),e=t.counter_compress_descendants.text(),e=parseInt(e,10)+1,t.counter_compress_descendants.text(e),e>0&&!c.is(":visible")&!d.is(":visible")&&d.show()},t.decrement_childs=function(){var e=t.counter_expand_descendants.text();e=parseInt(e,10)-1,t.counter_expand_descendants.text(e),e=t.counter_compress_descendants.text(),e=parseInt(e,10)-1,t.counter_compress_descendants.text(e),e<1&&(c.is(":visible")||d.is(":visible"))&&d.hide()};var S=t.find(".get_user_agent").first();function E(){var t=bootbox.dialog({message:" ",closeButton:!0,backdrop:!0}).init((function(){var e=t.find(".bootbox-body");e.html('<div class="spinner" style="text-align: center; font-size:48px;"><i class="fas fa-spinner fa-spin"></i></div>'),$.ajax({method:"GET",url:"/user_agents/comment/"+i}).done((function(t){e.find(".spinner").remove(),e.html(t)})).always((function(){}))}))}S.unbind("click").on("click",E);var j=t.find(".private_status").first();k.unbind("click").on("click",(function(t){t.preventDefault(),t.stopPropagation(),k.addClass("loading-cap"),$.ajax({method:"GET",url:k.attr("href")}).done((function(t){k.hide(),j.remove()})).fail((function(){})).always((function(){k.removeClass("loading-cap")}))}))}},"Ss+v":function(t,e,i){"use strict";function n(){var t=this;this.init=function(e,i){t.parent=e,t.url=i,t.type=t.parent.data("type"),t.id=t.parent.data("id"),t.loading_content=t.parent.find("[data-status=loading]"),t.exists_content=t.parent.find("[data-status=exists]"),t.not_exists_content=t.parent.find("[data-status=not_exists]"),t.count=t.parent.find(".count"),t.parent.on("click",t.onClick)},this.onClick=function(e){t.parent.removeClass("active").attr("disabled","disabled"),t.isPressed()?t.decrement():t.increment(),t.loading_content.show(),t.exists_content.hide(),t.not_exists_content.hide(),$.ajax({method:"GET",url:t.url,dataType:"json"}).done(t.onDone).fail(t.onFail)},this.onDone=function(e){t.loading_content.hide(),t.parent.removeAttr("disabled"),"attached"==e.result?(t.exists_content.show(),t.parent.attr("aria-pressed","true")):(t.not_exists_content.show(),t.parent.attr("aria-pressed","false")),t.setCount(e.added_to_favorites_count)},this.onFail=function(){t.loading_content.hide(),t.parent.removeAttr("disabled"),t.not_exists_content.show(),t.setCount(0)},this.setCount=function(e){(e=parseInt(e))>0?(t.count.hide(),t.count.text(e)):(t.count.hide(),t.count.text(""))},this.getCount=function(){return parseInt(t.count.text())},this.isPressed=function(){return"true"===t.parent.attr("aria-pressed")},this.increment=function(){var e=t.getCount();t.setCount(e+1)},this.decrement=function(){var e=t.getCount();t.setCount(e-1)}}i.d(e,"a",(function(){return n}))},ZI2Y:function(t,e,i){"use strict";function n(){var t=this;this.init=function(e){t.button=e,t.title=e.data("title"),t.description=e.data("description"),t.url=e.data("url"),t.image=e.data("image"),t.dialog=bootbox.dialog({message:'<div class="spinner" style="text-align: center; font-size:48px;"><i class="fas fa-spinner fa-spin"></i></div>',closeButton:!0,backdrop:!0,show:!1,size:"small"}),t.button.unbind("click").on("click",(function(){t.dialog.modal("show")})),t.dialog.on("shown.bs.modal",(function(e){t.onDialogOpen(e)}))},this.onDialogOpen=function(e){var i='<div data-direction="vertical" data-size="m" class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,';void 0!==t.image&&(i+="pinterest,"),i+='twitter,telegram,whatsapp,viber,moimir,blogger,delicious,digg,reddit,evernote,linkedin,lj,pocket,qzone,renren,sinaWeibo,surfingbird,tencentWeibo,tumblr,skype" data-counter="true" data-title="'+t.title+'" data-description="'+t.description+'" ',void 0!==t.image&&(i+='data-image="'+t.image+'" '),i+='data-url="'+t.url+'">';$(e.relatedTarget);t.dialog.find(".bootbox-body").html(i),$.getScript("//cdn.jsdelivr.net/npm/yandex-share2/share.js",(function(t,e,i){}))}}i.d(e,"a",(function(){return n}))},cWvU:function(t,e,i){"use strict";i.r(e),i.d(e,"default",(function(){return a}));var n=i("q48l"),o=i("Ss+v"),s=i("kW2s"),r=i("f3RH");function a(){var t=this;this.init=function(){t.container=$(".sequence"),t.container.length&&(t.id=window.sharedData.sequence_id,t.likeButton=t.container.find(".like").first(),t.favoriteButton=$(".user_library").first(),t.description=t.container.find("#description"),t.expandBiography=t.container.find(".expand-biography").first(),t.compressBiography=t.container.find(".compress-biography").first(),t.tabs=t.container.find("#sequenceTab"),t.loaderHtml='<div class="text-center py-5 px-2"><h1 class="fas fa-spinner fa-spin"></h1></div>',t.description.length>0&&t.description.htmlExpand({maxHeight:100,expand_button:t.expandBiography,compress_button:t.compressBiography,onExpand:function(){},onCompress:function(){$(window).scrollTo(t.description)}}),(new s.a).init(t.likeButton),(new o.a).init(t.favoriteButton,"/sequences/"+t.id+"/toggle_my_library"),t.booksTabContent=t.container.find("#books").first(),t.commentsTabContent=t.container.find("#comments").first(),t.table(),t.tabs.find('a[href="#books"]').unbind("click").bind("click",t.onBooksTabClick),t.tabs.find('a[href="#comments"]').unbind("click").bind("click",t.onCommentsTabClick))},this.onBooksTabClick=function(e){e.preventDefault(),t.booksTabContent.find(".table").length||t.booksTabContent.load("/sequences/"+t.id+"/books?with_panel=true",(function(){t.table()}))},this.onCommentsTabClick=function(e){e.preventDefault(),$(this).tab("show");var i=t.container.find($(this).attr("href"));i.find(".comments-search-container").length||(i.append(t.loaderHtml),i.load("/sequences/"+t.id+"/comments?with_panel=true",(function(){Object(n.a)(i.find(".comments-search-container"))})))},this.table=function(){var e=t.booksTabContent.find(".table"),i=e.parent().first(),n=t.booksTabContent.find("input.search").first();e.length>0&&e.tablesorter({widgets:["filter"],widgetOptions:{filter_external:n,filter_defaultFilter:{1:"~{query}"},filter_columnFilters:!1}}).bind("tablesorter-ready",(function(t){if(!window.isTouchDevice()){new r.a({viewport:i.get(0),content:e.get(0),mode:"x",bounce:!1,textSelection:!0,onUpdate:function(t){e.get(0).style.transform="translateX(".concat(-t.position.x,"px)")}});var n=i.width();e.width()>n&&e.css("cursor","move")}}))}}(new a).init()},f3RH:function(t,e,i){"use strict";function n(t,e){var i=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),i.push.apply(i,n)}return i}function o(t){for(var e=1;e<arguments.length;e++){var i=null!=arguments[e]?arguments[e]:{};e%2?n(Object(i),!0).forEach((function(e){s(t,e,i[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(i)):n(Object(i)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(i,e))}))}return t}function s(t,e,i){return e in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}function r(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){for(var i=0;i<e.length;i++){var n=e[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}i.d(e,"a",(function(){return d}));var c=function(t){return Math.max(t.offsetHeight,t.scrollHeight)},d=function(){function t(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};r(this,t);var i={content:e.viewport.children[0],direction:"all",pointerMode:"all",scrollMode:void 0,bounce:!0,bounceForce:.1,friction:.05,textSelection:!1,inputsFocus:!0,emulateScroll:!1,onClick:function(){},onUpdate:function(){},shouldScroll:function(){return!0}};if(this.props=o({},i,{},e),this.props.viewport&&this.props.viewport instanceof Element&&this.props.content){this.isDragging=!1,this.isTargetScroll=!1,this.isScrolling=!1,this.isRunning=!1;var n={x:0,y:0};this.position=o({},n),this.velocity=o({},n),this.dragStartPosition=o({},n),this.dragOffset=o({},n),this.dragPosition=o({},n),this.targetPosition=o({},n),this.scrollOffset=o({},n),this.rafID=null,this.events={},this.updateMetrics(),this.handleEvents()}}var e,i,n;return e=t,(i=[{key:"updateOptions",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.props=o({},this.props,{},t),this.props.onUpdate(this.getState()),this.startAnimationLoop()}},{key:"updateMetrics",value:function(){var t;this.viewport={width:this.props.viewport.clientWidth,height:this.props.viewport.clientHeight},this.content={width:(t=this.props.content,Math.max(t.offsetWidth,t.scrollWidth)),height:c(this.props.content)},this.edgeX={from:Math.min(-this.content.width+this.viewport.width,0),to:0},this.edgeY={from:Math.min(-this.content.height+this.viewport.height,0),to:0},this.props.onUpdate(this.getState()),this.startAnimationLoop()}},{key:"startAnimationLoop",value:function(){var t=this;this.isRunning=!0,cancelAnimationFrame(this.rafID),this.rafID=requestAnimationFrame((function(){return t.animate()}))}},{key:"animate",value:function(){var t=this;if(this.isRunning){this.updateScrollPosition(),this.isMoving()||(this.isRunning=!1,this.isTargetScroll=!1);var e=this.getState();this.setContentPosition(e),this.props.onUpdate(e),this.rafID=requestAnimationFrame((function(){return t.animate()}))}}},{key:"updateScrollPosition",value:function(){this.applyEdgeForce(),this.applyDragForce(),this.applyScrollForce(),this.applyTargetForce();var t=1-this.props.friction;this.velocity.x*=t,this.velocity.y*=t,"vertical"!==this.props.direction&&(this.position.x+=this.velocity.x),"horizontal"!==this.props.direction&&(this.position.y+=this.velocity.y),this.props.bounce&&!this.isScrolling||this.isTargetScroll||(this.position.x=Math.max(Math.min(this.position.x,this.edgeX.to),this.edgeX.from),this.position.y=Math.max(Math.min(this.position.y,this.edgeY.to),this.edgeY.from))}},{key:"applyForce",value:function(t){this.velocity.x+=t.x,this.velocity.y+=t.y}},{key:"applyEdgeForce",value:function(){if(this.props.bounce&&!this.isDragging){var t=this.position.x<this.edgeX.from,e=this.position.x>this.edgeX.to,i=this.position.y<this.edgeY.from,n=this.position.y>this.edgeY.to,o=t||e,s=i||n;if(o||s){var r=t?this.edgeX.from:this.edgeX.to,a=i?this.edgeY.from:this.edgeY.to,c=r-this.position.x,d=a-this.position.y,l={x:c*this.props.bounceForce,y:d*this.props.bounceForce},u=this.position.x+(this.velocity.x+l.x)/this.props.friction,p=this.position.y+(this.velocity.y+l.y)/this.props.friction;(t&&u>=this.edgeX.from||e&&u<=this.edgeX.to)&&(l.x=c*this.props.bounceForce-this.velocity.x),(i&&p>=this.edgeY.from||n&&p<=this.edgeY.to)&&(l.y=d*this.props.bounceForce-this.velocity.y),this.applyForce({x:o?l.x:0,y:s?l.y:0})}}}},{key:"applyDragForce",value:function(){if(this.isDragging){var t=this.dragPosition.x-this.position.x,e=this.dragPosition.y-this.position.y;this.applyForce({x:t-this.velocity.x,y:e-this.velocity.y})}}},{key:"applyScrollForce",value:function(){this.isScrolling&&(this.applyForce({x:this.scrollOffset.x-this.velocity.x,y:this.scrollOffset.y-this.velocity.y}),this.scrollOffset.x=0,this.scrollOffset.y=0)}},{key:"applyTargetForce",value:function(){this.isTargetScroll&&this.applyForce({x:.08*(this.targetPosition.x-this.position.x)-this.velocity.x,y:.08*(this.targetPosition.y-this.position.y)-this.velocity.y})}},{key:"isMoving",value:function(){return this.isDragging||this.isScrolling||Math.abs(this.velocity.x)>=.01||Math.abs(this.velocity.y)>=.01}},{key:"scrollTo",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.isTargetScroll=!0,this.targetPosition.x=-t.x||0,this.targetPosition.y=-t.y||0,this.startAnimationLoop()}},{key:"setPosition",value:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.velocity.x=0,this.velocity.y=0,this.position.x=-t.x||0,this.position.y=-t.y||0,this.startAnimationLoop()}},{key:"getState",value:function(){return{isMoving:this.isMoving(),isDragging:!(!this.dragOffset.x&&!this.dragOffset.y),position:{x:-this.position.x,y:-this.position.y},dragOffset:this.dragOffset,borderCollision:{left:this.position.x>=this.edgeX.to,right:this.position.x<=this.edgeX.from,top:this.position.y>=this.edgeY.to,bottom:this.position.y<=this.edgeY.from}}}},{key:"setContentPosition",value:function(t){"transform"===this.props.scrollMode&&(this.props.content.style.transform="translate(".concat(-t.position.x,"px, ").concat(-t.position.y,"px)")),"native"===this.props.scrollMode&&(this.props.viewport.scrollTop=t.position.y,this.props.viewport.scrollLeft=t.position.x)}},{key:"handleEvents",value:function(){var t=this,e={x:0,y:0},i=null,n=!1,o=function(i){if(t.isDragging){var o=n?i.touches[0].pageX:i.pageX,s=n?i.touches[0].pageY:i.pageY;t.dragOffset.x=o-e.x,t.dragOffset.y=s-e.y,t.dragPosition.x=t.dragStartPosition.x+t.dragOffset.x,t.dragPosition.y=t.dragStartPosition.y+t.dragOffset.y}};this.events.pointerdown=function(i){var s=(n=!(!i.touches||!i.touches[0]))?i.touches[0]:i,r=s.pageX,a=s.pageY,c=s.clientX,d=s.clientY,l=t.props.viewport,u=l.getBoundingClientRect();if(!(c-u.left>=l.clientLeft+l.clientWidth)&&!(d-u.top>=l.clientTop+l.clientHeight)&&t.props.shouldScroll(t.getState(),i)&&2!==i.button&&("mouse"!==t.props.pointerMode||!n)&&("touch"!==t.props.pointerMode||n)&&!(t.props.inputsFocus&&["input","textarea","button","select","label"].indexOf(i.target.nodeName.toLowerCase())>-1)){if(t.props.textSelection){if(function(t,e,i){for(var n=t.childNodes,o=document.createRange(),s=0;s<n.length;s++){var r=n[s];if(3===r.nodeType){o.selectNodeContents(r);var a=o.getBoundingClientRect();if(e>=a.left&&i>=a.top&&e<=a.right&&i<=a.bottom)return r}}return!1}(i.target,c,d))return;(p=window.getSelection?window.getSelection():document.selection)&&(p.removeAllRanges?p.removeAllRanges():p.empty&&p.empty())}var p;t.isDragging=!0,e.x=r,e.y=a,t.dragStartPosition.x=t.position.x,t.dragStartPosition.y=t.position.y,o(i),t.startAnimationLoop(),i.preventDefault()}},this.events.pointermove=function(t){o(t)},this.events.pointerup=function(){t.isDragging=!1},this.events.wheel=function(e){t.props.emulateScroll&&(t.velocity.x=0,t.velocity.y=0,t.isScrolling=!0,t.scrollOffset.x=-e.deltaX,t.scrollOffset.y=-e.deltaY,t.startAnimationLoop(),clearTimeout(i),i=setTimeout((function(){return t.isScrolling=!1}),80),e.preventDefault())},this.events.scroll=function(){var e=t.props.viewport,i=e.scrollLeft,n=e.scrollTop;Math.abs(t.position.x+i)>3&&(t.position.x=-i,t.velocity.x=0),Math.abs(t.position.y+n)>3&&(t.position.y=-n,t.velocity.y=0)},this.events.click=function(e){var i=t.getState();Math.abs(Math.max(i.dragOffset.x,i.dragOffset.y))>5&&(e.preventDefault(),e.stopPropagation()),t.props.onClick(i,e)},this.events.contentLoad=function(){return t.updateMetrics()},this.events.resize=function(){return t.updateMetrics()},this.props.viewport.addEventListener("mousedown",this.events.pointerdown),this.props.viewport.addEventListener("touchstart",this.events.pointerdown),this.props.viewport.addEventListener("click",this.events.click),this.props.viewport.addEventListener("wheel",this.events.wheel),this.props.viewport.addEventListener("scroll",this.events.scroll),this.props.content.addEventListener("load",this.events.contentLoad,!0),window.addEventListener("mousemove",this.events.pointermove),window.addEventListener("touchmove",this.events.pointermove),window.addEventListener("mouseup",this.events.pointerup),window.addEventListener("touchend",this.events.pointerup),window.addEventListener("resize",this.events.resize)}},{key:"destroy",value:function(){this.props.viewport.removeEventListener("mousedown",this.events.pointerdown),this.props.viewport.removeEventListener("touchstart",this.events.pointerdown),this.props.viewport.removeEventListener("click",this.events.click),this.props.viewport.removeEventListener("wheel",this.events.wheel),this.props.viewport.removeEventListener("scroll",this.events.scroll),this.props.content.removeEventListener("load",this.events.contentLoad),window.removeEventListener("mousemove",this.events.pointermove),window.removeEventListener("touchmove",this.events.pointermove),window.removeEventListener("mouseup",this.events.pointerup),window.removeEventListener("touchend",this.events.pointerup),window.removeEventListener("resize",this.events.resize)}}])&&a(e.prototype,i),n&&a(e,n),t}()},kW2s:function(t,e,i){"use strict";function n(t){return(n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function o(){var t=this;this.init=function(e){t.like=e,t.like.btn_liked=t.like.find("button.liked"),t.like.btn_empty=t.like.find("button.empty"),t.like.counter=t.like.find(".counter"),t.like.last_liked_status=t.like.data("liked"),t.likeable_type=t.like.data("likeable-type"),t.likeable_id=t.like.data("likeable-id"),t.likeable_create_user_id=t.like.data("likeable-create-user-id"),t.mouseOverDelay=1e3,t.mouseLeaveDelay=1e3,t.like.btn_liked.unbind("click").on("click",t.onClick),t.like.btn_empty.unbind("click").on("click",t.onClick),t.like.popover({boundary:"window",placement:"top",html:!0,trigger:"manual",animation:!1}),t.like.unbind("shown.bs.popover").bind("shown.bs.popover",t.onPopoverShown),t.like.unbind("mouseover").bind("mouseover",t.onButtonMouseOver),t.like.unbind("mouseleave").bind("mouseleave",t.onButtonMouseLeave)},this.onClick=function(){var e=$(this);t.isLiked()?t.decrementCounter():t.incrementCounter(),e.addClass("loading-cap"),t.setPopoverHtml('<i class="fas fa-spinner fa-spin"></i>'),t.getCount()>0&&t.showPopover(),t.request(e)},this.onButtonMouseOver=function(){clearTimeout(t.mouseOverTimer),t.mouseOverTimer=setTimeout((function(){!t.isMouseAway()&&t.getCount()>0&&(t.load(),t.isDataLoaded()&&t.showPopover())}),t.mouseOverDelay)},this.onButtonMouseLeave=function(){clearTimeout(t.mouseLeaveTimer),t.mouseLeaveTimer=setTimeout((function(){t.hidePopoverIfMouseAwayAndPopoverOpened()}),t.mouseLeaveDelay)},this.onPopoverShown=function(){t.getPopover().unbind("mouseleave").bind("mouseleave",t.onPopoverMouseLeave)},this.onPopoverMouseLeave=function(){clearTimeout(t.mouseLeaveTimer),t.mouseLeaveTimer=setTimeout((function(){t.hidePopoverIfMouseAwayAndPopoverOpened()}),t.mouseLeaveDelay)},this.hidePopoverIfMouseAwayAndPopoverOpened=function(){t.isMouseAway()&&t.hidePopover()},this.request=function(e){$.ajax({method:"GET",url:"/likes/"+t.likeable_type+"/"+t.likeable_id}).done(t.onDoneLoadClick).fail(t.fail).always((function(){e.removeClass("loading-cap")}))},this.onDoneLoadClick=function(e){e.like.id?e.like.deleted_at?t.toggleToEmpty():t.toggleToLiked():t.toggleToEmpty(),t.setCount(e.item.like_count),t.getCount()<1?t.hidePopover():t.refreshTooltipContent(e.latest_likes_html)},this.fail=function(e){t.hidePopover(),t.isLiked()?t.incrementCounter():t.decrementCounter()},this.refreshTooltipContent=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"";""!==e?(t.dataLoaded(),t.setPopoverHtml(e)):(t.dataNotLoaded(),t.load())},this.load=function(){t.like.data("ajax-start")||t.isDataLoaded()||$.ajax({url:"/likes/"+t.likeable_type+"/"+t.likeable_id+"/tooltip",cache:!0,beforeSend:function(e){t.showPopover(),t.setPopoverHtml('<i class="fas fa-spinner fa-spin"></i>'),t.like.data("ajax-start",!0)}}).done((function(e){t.setPopoverHtml(e),t.dataLoaded()})).always((function(){t.like.data("ajax-start",!1)}))},this.incrementCounter=function(){var e=t.getCount();t.setCount(e+1)},this.decrementCounter=function(){var e=t.getCount();t.setCount(e-1)},this.setCount=function(e){e=parseInt(e),t.like.counter.text(e),e<1?t.like.counter.hide():t.like.counter.show()},this.toggleToLiked=function(){t.like.btn_liked.show(),t.like.btn_empty.hide()},this.toggleToEmpty=function(){t.like.btn_liked.hide(),t.like.btn_empty.show()},this.setPopoverHtml=function(e){var i=$("<div>"+e+"</div>");i.find("img.lazyload").each((function(){$(this).attr("src",$(this).data("src"))}));var n=t.like.attr("aria-describedby");t.isPopoverOpened()&&$("#"+n).find(".popover-body").html(i.html()),t.like.attr("data-content",i.html()),t.updatePopover()},this.dataLoaded=function(){t.like.data("data-loaded",!0)},this.dataNotLoaded=function(){t.like.data("data-loaded",!1)},this.isDataLoaded=function(){return!!t.like.data("data-loaded")},this.isPopoverOpened=function(){var e=t.like.attr("aria-describedby");return"undefined"!==n(e)&&!1!==e},this.isMouseAway=function(){var e=t.like.attr("aria-describedby");return!t.like.is(":hover")&&0===$("#"+e+":hover").length},this.isLiked=function(){return!!t.like.btn_liked.is(":visible")},this.isTouchDevice=function(){return window.isTouchDevice()},this.getCount=function(){return parseInt(t.like.counter.first().text(),10)},this.getPopover=function(){var e=t.like.attr("aria-describedby");return $("#"+e).first()},this.showPopover=function(){t.isPopoverOpened()||(t.like.popover("show"),t.like.popover("update"))},this.hidePopover=function(){t.isPopoverOpened()&&t.like.popover("hide")},this.updatePopover=function(){t.isPopoverOpened()&&t.like.popover("update")}}i.d(e,"a",(function(){return o}))},q48l:function(t,e,i){"use strict";i.d(e,"a",(function(){return o}));var n=i("FzaM");function o(t){var e=t.find(".list"),i=t.find("form");function o(){window.paginationScrollToActive(),e.find(".item").each((function(){Object(n.a)($(this))}))}function s(){e.off("click",".pagination a").on("click",".pagination a",(function(t){t.preventDefault(),e.addClass("loading-cap");var i=$(this).attr("href");window.history.pushState("","",i),$.ajax({url:i,data:{ajax:!0}}).done((function(t){e.removeClass("loading-cap"),e.html(t),o(),$("html, body").animate({scrollTop:e.offset().top-80},100)})).fail((function(){401==jqXHR.status&&location.reload()}))}))}s(),o(),i.formChange({timeout:500,onShow:function(){$(this).ajaxSubmit({beforeSubmit:function(t,i,n){return e.addClass("loading-cap"),t=$.grep(t,(function(t){return""!=t.value})),history.pushState("","",i.attr("action")+"?"+$.param(t)),!0},success:function(t,i,n,r){e.removeClass("loading-cap"),e.html(t),s(),o()},error:function(t,e,i,n){401==t.status&&location.reload()}})}})}}});