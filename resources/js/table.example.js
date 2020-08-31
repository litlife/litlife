import ScrollBooster from 'scrollbooster';


$(function () {

	let table_wrapper = $('.table-responsive').first();
	let table = table_wrapper.find("#table").first();

	table
		.tablesorter({})
		.bind('tablesorter-ready', function (e) {

			if (!window.isTouchDevice()) {
				let sb = new ScrollBooster({
					viewport: table_wrapper.get(0), // required
					content: table.get(0), // scrollable element
					mode: 'x', // scroll only in horizontal dimension
					bounce: false,
					textSelection: true,
					onUpdate: (data) => {
						// your scroll logic goes here
						table.get(0).style.transform = `translateX(${-data.position.x}px)`
					}
				});

				let table_wrapper_width = table_wrapper.width();
				let table_width = table.width();

				if (table_width > table_wrapper_width) {
					table.css('cursor', 'move');
				}
			}
		});
});

/*
    let header = $('header').first();

    let offset = -7;

    if (header.css("position") === 'sticky') {
        offset = (header.outerHeight() - 10);
    }

    let table = $('#table');

    table.DataTable().destroy();

    table = table.DataTable({
        responsive: false,
        paging: false,
        info: false,
        searching: false,
        scrollX: true,
        "initComplete": function(settings, json){

            let dataTables_scrollBody = $('.dataTables_scrollBody').first().get(0);
            let table = $('.table').first().get(0);

            console.log(dataTables_scrollBody);
            console.log(table);

            let sb = new ScrollBooster({
                viewport: dataTables_scrollBody, // required
                content: table, // scrollable element
                mode: 'x', // scroll only in horizontal dimension
                onUpdate: (data)=> {
                    // your scroll logic goes here
                    table.style.transform = `translateX(${-data.position.x}px)`
                }
            })
        }
    });


table.on( 'draw', function () {
    console.log( 'draw ');
})
.on( 'init.dt', function () {
        console.log( 'init.dt ');
}).on( 'init', function () {
    console.log( 'init.dt ');
});

*/
