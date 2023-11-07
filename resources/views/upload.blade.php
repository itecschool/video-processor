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

	<script>

		const token = document.getElementById('token').value;

	    function MultipartUploader() {
	        
	        this.isPaused = false;
	        
	        this.currentPartNumber = 1;
	        
	        this.maxRetries = 3;
	        
	        this.chunkSize = 5 * 1024 * 1024; // 5MB
	        
	        this.parts = [];
	        
	        this.file = null;
	        
	        this.uploadId = null;

	        this.filename = 'videos/aaaaaa/original.mp4'; // Personalizar según corresponda

	    }

	    MultipartUploader.prototype.startUpload = async function(file) {

	        this.file = file;
	        
	        const totalParts = Math.ceil(this.file.size / this.chunkSize);

	        const response = await fetch('{{ route('videoprocessor.initiate.upload') }}', {

	            method: 'POST',
	            
	            body: JSON.stringify({ 
	            
	            	_token: token, 
	            
	            	filename: this.filename 
	            
	            }),
	            
	            headers: { 

	            	'Content-Type': 'application/json' 
	            
	            },

	        });
	        
	        const data = await response.json();

	        this.uploadId = data.uploadId;

	        await this.uploadParts(totalParts);
	    }

	    MultipartUploader.prototype.uploadParts = async function(totalParts) {

	        while (this.currentPartNumber <= totalParts && !this.isPaused) {
	        
	            const retries = 0;
	        
	            let success = false;

	            while (retries < this.maxRetries && !success) {

	                try {
	                
	                    await this.uploadPart(this.currentPartNumber);
	                
	                    success = true;
	                
	                } catch (error) {
	                
	                    retries++;
	                
	                    if (retries === this.maxRetries) {
	                
	                        throw new Error(`Failed uploading part ${this.currentPartNumber} after ${this.maxRetries} retries.`);
	                
	                    }
	                
	                }

	            }

	            this.currentPartNumber++;
	        
	        }

	        if (this.currentPartNumber > totalParts) {
	        
	            await this.completeUpload();
	        
	        }

	    }

	    MultipartUploader.prototype.uploadPart = async function(partNumber) {

	        const start = (partNumber - 1) * this.chunkSize;
	        
	        const end = partNumber * this.chunkSize;
	        
	        const blob = this.file.slice(start, end);

	        const signedResponse = await fetch('{{ route('videoprocessor.sign.part.upload') }}', {
	        
	            method: 'POST',
	        
	            body: JSON.stringify({
	        
	            	_token: token, 
	        
	            	filename: this.filename, 
	        
	            	uploadId: this.uploadId, 
	        
	            	partNumber 
	        
	            }),
	        
	            headers: { 

	            	'Content-Type': 'application/json' 
	            },
	        
	        });

	        console.log(signedResponse);

	        const { url } = await signedResponse.json(); // Esta es la URL firmada

	        const uploadResponse = await fetch(url, {
	            
	            method: 'PUT',
	            
	            body: blob,

	        });
	        
	        if (!uploadResponse.ok) {
	            
	            throw new Error(`Failed uploading part ${partNumber}`);

	        }

	        this.parts.push({ ETag: uploadResponse.headers.get('ETag'), PartNumber: partNumber });

	    }

	    MultipartUploader.prototype.pauseUpload = function() {
	    
	        this.isPaused = true;
	    
	    }

	    MultipartUploader.prototype.resumeUpload = async function() {
	        
	        this.isPaused = false;
	        
	        const totalParts = Math.ceil(this.file.size / this.chunkSize);
	        
	        await this.uploadParts(totalParts);

	    }

	    MultipartUploader.prototype.completeUpload = async function() {
	        
	        await fetch('{{ route('videoprocessor.complete.upload') }}', {

	            method: 'POST',

	            body: JSON.stringify({ 

	            	_token: token, 

	            	filename: this.filename, 

	            	uploadId: this.uploadId, 

	            	parts: this.parts 

	            }),

	            headers: { 'Content-Type': 'application/json' },

	        });

	    }

	    // Ejemplo de uso:
	    let uploader;

	    function startFileUpload() {

	        uploader = new MultipartUploader();

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