import ManagerItem from "./components/manager/item";

$('.managers').find('.item').each(function () {
	new ManagerItem().init($(this));
});