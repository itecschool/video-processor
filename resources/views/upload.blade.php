<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title></title>
</head>
<body>

	<!-- Formulario para cargar un archivo -->
	<form id="uploadForm">
		<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
	    <input type="file" id="videoFile">
	    <button type="button" onclick="startFileUpload()">Iniciar Carga</button>
	</form>

	<!-- Botones para pausar y reanudar la carga -->
	<button onclick="pauseUpload()">Pausar Carga</button>
	<button onclick="resumeUpload()">Reanudar Carga</button>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.1/axios.min.js"></script>
	<script src="https://unpkg.com/innoboxrr-multipart-uploader@1.0.3/index.js"></script>

	<script>

		const token = document.getElementById('token').value;

	    let uploader;

	    function startFileUpload() {

	    	// La generación de este valor es con fines de prueba:
	    	let random = 'fasdfasdfq3n4fi';

	        uploader = new MultipartUploader(random, {
	        	initiateUploadRoute: '{{ route('videoprocessor.initiate.upload') }}',
	        	signPartUploadRoute: '{{ route('videoprocessor.sign.part.upload') }}',
	        	completeUploadRoute: '{{ route('videoprocessor.complete.upload') }}',
	        });

	        const fileInput = document.getElementById('videoFile');

	        const file = fileInput.files[0];

	        uploader.startUpload(file);

	    }

	    function pauseUpload() {

	        if (uploader) {
	        
	            uploader.pauseUpload();
	        
	        }

	    }

	    function resumeUpload() {
	        
	        if (uploader) {
	        
	            uploader.resumeUpload();
	        
	        }

	    }

	</script>

</body>
</html>