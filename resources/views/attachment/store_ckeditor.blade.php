<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>
<body>


<script type='text/javascript'>

			@if (empty($url))
	var url = null;
			@else
	var url = '{{ $url }}';
			@endif

			@if (empty($message))
	var message = null;
			@else
	var message = '{{ $message }}';
	@endif

	window.parent.CKEDITOR.tools.callFunction(
		'{{ request()->input('CKEditorFuncNum') }}', url, message);

	window.close();

</script>

</body>
</html>