<!DOCTYPE html>
<html>
	<head>
		<title>Attach Example</title>
	</head>
	<body>
		
		{{ Form::open_for_files(null) }}
		<p>
			{{ Form::file('userfile') }}
		</p>
		<p>
			{{ Form::submit('Upload') }}
		</p>
		{{ Form::close() }}
		
	</body>
</html>
