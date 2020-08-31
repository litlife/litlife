export default function sisyphus_remove_by_name(name) {

	RegExp.quote = function (str) {
		return str.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
	};

	Object.keys(localStorage)
		.forEach(function (key) {
			console.log(key);

			var str = '[name=' + name + ']';

			if (key.search(new RegExp(RegExp.quote(str))) > 0) {
				console.log('remove ' + key);
				localStorage.removeItem(key);
			}
		});

}
