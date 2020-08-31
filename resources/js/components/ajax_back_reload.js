var latest_document_location = document.location.href;

window.onpopstate = function (event) {
	//console.log("document location: " + document.location.href);

	if (latest_document_location != document.location.href) {
		console.log("latest_document_location: " + latest_document_location + ' document.location.href:' + document.location.href);
		latest_document_location = document.location.href;
		document.location.href = document.location.href;
	}
};