import item from "../components/comment/item";
import bell_toggle_button from "../components/bell_toglle_button";


$('.comments').find(".item").each(function () {
	item($(this));
});

let btn_bell = new bell_toggle_button;
btn_bell.init($('.btn-bell-toggle').first());
