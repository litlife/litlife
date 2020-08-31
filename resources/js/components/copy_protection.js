export default function copy_protection(element) {

	if (element instanceof jQuery) {
		element = element.get(0);
	}

	//console.log(element);
	element.ondblclick = noselect;
	element.onselectstart = noselect;
	element.onmousedown = noselect;

	function noselect() {
		//console.log('noselect');
		return false;
	}
}

