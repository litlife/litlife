import Like from "../Like";
import shareButton from "../share_button";
import favorite_toggle_button from "../favorite_toggle_button";

export default function item() {

	this.init = function (i, $parent = null) {

		let self = this;

		self.i = i;
		self.id = i.data("id");
		self.$id = self.id;
		self.$btn_delete = i.find('.delete').first();
		self.$btn_restore = i.find('.restore').first();
		self.$btn_get_link = i.find('.get_link').first();
		self.$btn_share = i.find('button.share').first();
		self.$btn_favorite_toggle = i.find('.btn-favorite').first();

		self.$btn_get_link.removeAttr('href').unbind("click").on('click', self.get_link);

		self.$btn_delete.unbind("click").on('click', function (event) {
			event.preventDefault();
			self.deleteOrRestore($(this));
		});

		self.$btn_restore.unbind("click").on('click', function (event) {
			event.preventDefault();
			self.deleteOrRestore($(this));
		});

		i.find('.like').each(function () {
			new Like().init($(this));
		});

		new shareButton().init(self.$btn_share);

		favorite_toggle_button(self.$btn_favorite_toggle);
	};

	this.deleteOrRestore = function (btn) {

		let self = this;

		btn.button('loading');

		btn.hide();

		let url = btn.attr('href');

		$.ajax({
			method: "DELETE",
			url: url
		}).done(function (msg) {
			self.onDelete(msg);
		}).fail(function () {
			self.onDeleteFail();
		});
	};

	this.get_link = function (e) {
		let self = this;
		e.preventDefault();
		bootbox.alert('<textarea class="form-control">' + self.$btn_get_link.data('href') + '</textarea>');
	};

	this.onDelete = function (msg) {

		let self = this;

		if (msg.deleted_at) {
			self.$btn_delete.hide();
			self.$btn_restore.show();
			self.i.addClass('transparency');
		} else {
			self.$btn_delete.show();
			self.$btn_restore.hide();
			self.i.removeClass('transparency');
		}

		self.$btn_restore.button('reset');
		self.$btn_delete.button('reset');
	};

	this.onDeleteFail = function () {

		let self = this;

		btn.show();
	}
}