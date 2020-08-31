<script type="text/javascript">

	$.extend(true, $.fn.dataTable.defaults, {
		"scrollX": true,
		language: {
			"processing": "{{ __('datatables.processing') }}",
			"search": "{{ __('datatables.search') }}",
			"lengthMenu": "{{ __('datatables.length_menu') }}",
			"info": "{{ __('datatables.info') }}",
			"infoEmpty": "{{ __('datatables.info_empty') }}",
			"infoFiltered": "{{ __('datatables.info_filtered') }}",
			"infoPostFix": "{{ __('datatables.info_post_fix') }}",
			"loadingRecords": "{{ __('datatables.loading_records') }}",
			"zeroRecords": "{{ __('datatables.zero_records') }}",
			"emptyTable": "{{ __('datatables.empty_table') }}",
			"paginate": {
				"first": "{{ __('datatables.first') }}",
				"previous": "{{ __('datatables.previous') }}",
				"next": "{{ __('datatables.next') }}",
				"last": "{{ __('datatables.last') }}"
			},
			"aria": {
				"sortAscending": "{{ __('datatables.sort_ascending') }}",
				"sortDescending": "{{ __('datatables.sort_descending') }}"
			}
		}
	});

</script>