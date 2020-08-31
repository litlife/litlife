/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('jquery.scrollto/jquery.scrollTo');
require('lazysizes/lazysizes');
require('scrollpos-styler/scrollPosStyler');

import AddScrollOffset from "./components/AddScrollOffset";
import em_to_px_convert from "./plugins/em_to_px_convert";
import htmlSorter from "./plugins/htmlSorter";
import pagination_set_current_page from "./components/pagination_set_current_page";
import pagination_set_per_page from "./components/pagination_set_per_page";
import scrollToTopBottom from "./components/back_to_top";
import IdeasCardHide from "./components/IdeasCardHide";
// show more text
import ShowMoreCollapse from './components/show_more_collapse';
import Search from "./components/search";

new AddScrollOffset().init();

/*
Библиотеки для работы сокетов

import Echo from "laravel-echo"

window.io = require('socket.io-client');

if (typeof io !== 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6001'
    });
}
*/

window.isTouchDevice = function () {
	return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
};

bootbox.setDefaults({
	animate: false,
	backdrop: true,
	onEscape: true
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//window.Vue = require('vue');


//Vue.component('bookmark', require('./components/Bookmark.vue'));
//Vue.component('dialog', require('./components/Dialog.vue'));

/*
 new Vue({
 el: '#app'
 });
 */

$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});


$(document).ajaxError(function (event, jqxhr, settings, thrownError) {

	console.log(thrownError);
	console.log(jqxhr);

	if (jqxhr.status == "401") {
		bootbox.alert('<div class="text-center">' + jqxhr.responseJSON.error + '</div>');
	}

	if (jqxhr.status == "403") {
		bootbox.alert('<div class="text-center">' + jqxhr.responseJSON.error + '</div>');
	}

	if (jqxhr.status == "419") {
		bootbox.alert('<div class="text-center">csrf error</div>');
	}
});

///

require('./plugins/form-change-plugin');
require('./plugins/html-expand-plugin');
require('./plugins/reverse-plugin');
require('./plugins/local-storage-array-support');

window.em_to_px_convert = em_to_px_convert;

window.htmlSorter = htmlSorter;

require('./components/bookmark/create');

new scrollToTopBottom().init();

require('./components/spoiler');
require('./components/qrcode_dialog');
require('./components/sidebar_toggle');
require('./components/sceditor_extensions');
require('./components/sidebar');
require('./components/anchor_offset');


pagination_set_current_page();

pagination_set_per_page();


/*
import sisyphus_remove_by_name from "./sisyphus_remove_by_name2";
window.sisyphus_remove_by_name = sisyphus_remove_by_name;

$(function () {
    var forms = $("form.sisyphus");

    forms.each(function(){
        var form = $(this);

        form.sisyphus({
            locationBased: false,
            autoRelease: false,
            excludeFields: form.find("[name=_token]"),
        });
    });
});
*/

/*
var visitortime = new Date();
var visitortimezone = "GMT " + -visitortime.getTimezoneOffset()/60;
console.log(visitortimezone);
*/

if (window.isTouchDevice() === false) {
	$('body').tooltip({
		selector: '[data-toggle=tooltip]',
		animation: false,
		delay: {"show": 500, "hide": 100}
	});
}

$(document).ajaxSend(function () {
	$('[data-toggle=tooltip]').tooltip('hide');
});

$(document).ajaxComplete(function () {
	window.removeDropdownsWhereItemsNotExists();
	pagination_set_current_page();
	pagination_set_per_page();
});

$.fn.select2.defaults.set("theme", "bootstrap4");


// breadcrumb scroll to the left

$('.breadcrumb-scroll').scrollLeft(99999);

// pagination scroll to active link

window.paginationScrollToActive = function () {
	$('.pagination').each(function () {
		var pagination = $(this);
		var active = pagination.find('.active');

		if (active.length) {
			//
			var pagination_width = pagination.width();
			var offsetLeft = active.get(0).offsetLeft;
			var offset = offsetLeft - pagination_width / 2;

			/*
						console.log('pagination_width: ' + pagination_width);
						console.log('offsetLeft: ' + offsetLeft);
					console.log('offset: ' + offset);
					console.log(active);
			*/
			pagination.scrollLeft(offset);
		}
	});
};

window.paginationScrollToActive();

window.removeDropdownsWhereItemsNotExists = function () {
	$('[data-toggle=dropdown]').each(function () {
		let button = $(this);
		let id = button.attr('id');
		let dropdown_menu = $('[aria-labelledby="' + id + '"]:first');

		//console.log(dropdown_menu.find('.dropdown-item:visible').length);
		//console.log(dropdown_menu.find('.dropdown-item').length);

		let hide = dropdown_menu.find('.dropdown-item').filter(function () {
			//console.log($(this).css('display'));
			if ($(this).css('display') != 'none')
				return $(this);
		});

		//console.log(hide.length);

		if (hide.length < 1)
			button.hide();
	});
};

window.removeDropdownsWhereItemsNotExists();

let instance = new ShowMoreCollapse();
instance.collapsed_elements = $('.collapse');
instance.init();

require('./components/ajax_back_reload');

// включить popover ы
$('[data-toggle="popover"]').popover({
	trigger: 'focus'
});

new IdeasCardHide().init($('#sidebar').find('.idea-card').first());

let search = new Search();
search.form = $('header #search_outter_form');
search.dialog = $('#common_search_modal');
search.init();