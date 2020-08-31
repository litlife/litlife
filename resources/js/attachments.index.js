$(".item").each(function () {

	var url = $(this).data("url");


	$(this).find('.insert').click(function () {
		returnFileUrl(url);
	});
});


