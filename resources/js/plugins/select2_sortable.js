export default function select2_sortable($select2) {
	var ul = $select2.next('.select2-container').find('ul.select2-selection__rendered');
	ul.sortable({
		handle: '.move',
		onDrop: function ($item, container, _super) {

			var $clonedItem = $('<div/>').css({height: 0});
			$item.before($clonedItem);
			$clonedItem.animate({'height': $item.height()});

			$item.animate($clonedItem.position(), function () {
				$clonedItem.detach();
				_super($item, container);
			});

			$($(ul).find('.select2-selection__choice').get().reverse()).each(function () {
				var id = $(this).data('data').id;
				var option = $select2.find('option[value="' + id + '"]')[0];
				$select2.prepend(option);
			});
		}
	})
}