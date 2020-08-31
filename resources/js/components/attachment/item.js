export default function item(i) {

	let id = i.data("id");

	let book_id = i.data("book-id");

	let url = i.data('url');
	let title = i.find('.title');

	// delete restore

	let $btn_delete = i.find('.delete');

	$btn_delete.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	let $btn_restore = i.find('.restore');

	$btn_restore.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	function deleteOrRestore(btn) {
		btn.button('loading');

		btn.hide();

		$.ajax({
			method: "DELETE",
			url: "/books/" + book_id + "/attachments/" + id + "/delete"
		}).done(function (msg) {

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				i.find('img').addClass('transparency');
				title.addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				i.find('img').removeClass('transparency');
				title.removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}

	i.find('.insert').click(function () {
		returnFileUrl(url);
	});

	// Helper function to get parameters from the query string.
	function getUrlParam(paramName) {
		var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
		var match = window.location.search.match(reParam);

		return (match && match.length > 1) ? match[1] : null;
	}

// Simulate user action of selecting a file to be returned to CKEditor.
	function returnFileUrl(fileUrl) {

		var funcNum = getUrlParam('CKEditorFuncNum');

		window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl, function () {
			// Get the reference to a dialog window.
			var dialog = this.getDialog();
			// Check if this is the Image Properties dialog window.
			if (dialog.getName() == 'image') {
				// Get the reference to a text field that stores the "alt" attribute.
				var element = dialog.getContentElement('info', 'txtAlt');
				// Assign the new value.
				if (element)
					element.setValue('alt text');
			}
			// Return "false" to stop further execution. In such case CKEditor will ignore the second argument ("fileUrl")
			// and the "onSelect" function assigned to the button that called the file manager (if defined).
			// return false;
		});
		window.close();
	}
}