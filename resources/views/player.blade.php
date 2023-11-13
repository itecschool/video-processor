
<!DOCTYPE html>

<html lang="es">

    <head>
        
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <title>{{ $video->name }}</title>
        
        <link href="/plugins/videojs8/examples/css/style.css" rel="stylesheet" type="text/css">
        <link href="/plugins/videojs8/skins/nuevo/videojs.min.css" rel="stylesheet" type="text/css" />

        <script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>      
        <script src="/plugins/videojs8/video.min.js"></script>
        <script src="/plugins/videojs8/nuevo.min.js?78"></script>
        <script src="/plugins/videojs8/plugins/videojs.hotkeys.min.js"></script>
        <script src="/plugins/videojs8/plugins/videojs.events.js"></script>
        <script src="/plugins/videojs8/plugins/videojs-chromecast.min.js"></script>

        <style type="text/css">

            * {
                margin: 0;
                padding: 0;
            }

            .video-container {
                position: absolute;
                top: 0;
                bottom: 0;
                width: 100%;
                height: 100%; 
                overflow: hidden;
            }

            .video-container .video-js {
                /* Make video to at least 100% wide and tall */
                min-width: 100%; 
                min-height: 100%; 

                /* Setting width & height to auto prevents the browser from stretching or squishing the video */
                width: auto;
                height: auto;

                /* Center the video */
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
            }

        </style>

    </head>

    <body>

        <div class="video-container">

            <video 
                id="player_{{ $video->id }}" 
                class="video-js" 
                controls 
                preload="auto" 
                playsinline 
                @if($video->thumbnail) poster="{{ $video->thumbnail }}" @endif>

                <!-- SOURCE -->
                <source 
                    type="application/x-mpegURL"
                    src="{{ route('videoprocessor.playlist', [
                        'code' => $video->code,
                        'filename' => 'master.m3u8'
                    ]) }}">

                <!-- CHAPTERS -->


                <track 
                    kind="chapters" 
                    src="/plugins/videojs8/examples/chapters/test-en.vtt" 
                    srclang="en">

                <track 
                    kind="chapters" 
                    src="/plugins/videojs8/examples/chapters/test-es.vtt" 
                    srclang="es">

                <!-- CAPTIONS -->

                <track 
                    kind="captions" 
                    src="/plugins/videojs8/examples/captions/en.vtt" 
                    srclang="en" 
                    label="English" default>

                <track 
                    kind="captions" 
                    src="/plugins/videojs8/examples/captions/es.vtt" 
                    srclang="es" 
                    label="Español">

            </video>

        </div>

        <script>

            var player = videojs('player_{{ $video->id }}');
            
            player.nuevo({ 

                // ** Data **
                title: "{{ $video->lesson->name }}",
                url: "{{ route('videoprocessor.player', ['code' => $video->code]) }}",
                embed: '<iframe src="{{ $video->oEmbed }}" width="640" height="360" frameborder="0" allowfullscreen></iframe>',
                
                // ** Video Info **
                videoInfo: true,
                infoIcon: "/plugins/videojs8/examples/assets/images/logo_small.png",  // optional
                infoUrl: "https://www.nuevodevel.com/nuevo/showcase/videoinfo",  // optional
                infoTitle: "{{ $video->lesson->name }}",
                infoDescription: "{{ $video->user->name }}",

                // ** Logos **
                logo: "/plugins/videojs8/examples/assets/images/logo.png", // (undefined)
                logocontrolbar: "https://laravelers.com/filesystem/2933.ico", // (undefined)
                logourl: "https://laravelers.com", // (undefined)
                logoposition: "RT", // (LT) RT: Right Top, LT: Left Top, BL: Bottom Left 
                target: '_blanck', // (_blanck) _self
                logotitle: "laravelers.com",
                
                // ** Player setup **
                relatedMenu: true, // (true)
                shareMenu: false, // (true)
                rateMenu: true, // (true)
                zoomMenu: true, // (true)
                settingsButton: true, // (true)
                controlbar: true, // (true)
                iosFullscreen: 'native', // (native) 'pseudo'
                androidLock: false, 
                pipButton: true, // Show/Hide PictureInPicture button
                ccButton: true,
                qualityMenu: false, // Creo que esto no funciona
                tooltips: true,
                hdicon: true, // Mestra la opción de HD en el menú de calidad
                chapterMarkers: true,
                touchControls: true,
                touchRewindForward: true,

                // ** Context menu **
                contextMenu: true,
                contextUrl: 'https://laravelers.com',
                contextText: '&copy; Laravelers.com',

                // ** Zoom **
                zoomInfo: true,
                zoomWheel: true,

                // ** Rewin/Forward **
                buttonRewind: true,
                buttonForward: true,
                mirrorButton: false,
                theaterButton: false,
                rewindforward: 10,

                // ** Start Time **
                startTime: undefined, // Define el tiempo en segundos donde comenzar el video
                
                // ** Resume **
                video_id: "{{ $video->uuid }}",
                resume: true, // Permite retomar el video desde donde se quedo.
                endAction: undefined,
                related: [], //  javascript array of related videos.

                // ** Sprite **
                // Docs: https://www.nuevodevel.com/nuevo/showcase/sprite
                /*
                slideImage: "/plugins/videojs8/examples/images/sprite.jpg",
                ghostThumb: true,
                */

                // ** Limit Image **
                /*
                limit: 5,
                limiturl: 'https://laravelers.com',
                limitimage: '/videojs/examples/images/limit.png',
                limitmessage: 'Your message text' // optional, 
                */

                // ** Snapshot **
                snapshot: true,
                snapshotWatermark: "laravelers.com",

            });

            player.hotkeys({
                volumeStep: 0.1,
                seekStep: 5
            });

            player.events({ analytics:true });

            player.on('track', (e, data) => {

                switch(data.event) {

                    case 'loaded':

                        let d = {

                            video_id: data.playerId,
                            
                            video_title: data.playerTitle,
                            
                            loadTime: data.initialLoadTime, //always 0 for live video

                        };

                        window.parent.postMessage({event: 'loadPlayer', data: d}, '*');
                                                             
                    break;

                    case 'firstPlay':

                        window.parent.postMessage({event: 'firstPlay', data: data}, '*');

                    break;

                    case 'pause':
                        
                        var pauseCount = data.pauseCount;

                        window.parent.postMessage({event: 'pause', data: pauseCount}, '*');

                    break;

                    case 'resume':
                        
                        var resumeCount = data.resumeCount;

                        window.parent.postMessage({event: 'resume', data: resumeCount}, '*');

                    break;

                    case 'buffered':
                        
                        var bufferTime = data.bufferTime;

                        window.parent.postMessage({event: 'buffered', data: bufferTime}, '*');

                    break;

                    case 'seek':
                        
                        var seekTo = data.seekTo;

                        window.parent.postMessage({event: 'seek', data: seekTo}, '*');

                    break;

                    case '10%':
                        
                        var currentTime = data.currentTime;

                        window.parent.postMessage({event: '10%', data: currentTime}, '*');

                    break;

                    case '25%':
                        
                        var currentTime = data.currentTime;

                        window.parent.postMessage({event: '25%', data: currentTime}, '*');

                    break;

                    case '50%':
                        
                        var currentTime = data.currentTime;

                        window.parent.postMessage({event: '50%', data: currentTime}, '*');

                    break;

                    case '75%':
                        
                        var currentTime = data.currentTime;
                        
                        window.parent.postMessage({event: '75%', data: currentTime}, '*');

                    break;

                    case '90%':
                        
                        var currentTime = data.currentTime;

                        window.parent.postMessage({event: '90%', data: currentTime}, '*');

                    break;

                    case 'mute':

                        window.parent.postMessage({event: 'mute', data: null}, '*');

                    break;

                    case 'unmute':

                        window.parent.postMessage({event: 'unmute', data: null}, '*');

                    break;

                    case 'rateChange':

                        var currentRate = data.rate;

                        window.parent.postMessage({event: 'rateChange', data: currentRate}, '*');

                    break;

                    case 'enterFullscreen':

                        window.parent.postMessage({event: 'enterFullscreen', data: null}, '*');

                    break;

                    case 'exitFullscreen':

                        window.parent.postMessage({event: 'exitFullscreen', data: null}, '*');

                    break;

                    case 'resolutionChange':

                        
                        var currentResolution = data.res;

                        window.parent.postMessage({event: 'resolutionChange', data: currentResolution}, '*');

                    break;

                    case 'summary':

                        let summary = {

                            pauseCount: data.pauseCount,
                            
                            resumeCount: data.resumeCount,
                            
                            bufferCount: data.bufferCount,
                            
                            videoDuration: data.totalDuration,
                            
                            total_bufferingDuration: data.bufferDuration,
                            
                            real_watch_time: data.watchedDuration,

                        }

                        window.parent.postMessage({event: 'summary', data: summary}, '*');

                    break;

                    case 'default':

                        window.parent.postMessage({event: 'default', data: data}, '*');

                    break;

                }

            });

            /*
            player.chromecast({ 
                overlayButton: false,
                sources: [{
                    src: '{{ route('videoprocessor.playlist', [
                        'code' => $video->code,
                        'filename' => 'master.m3u8'
                    ]) }}',
                    type: 'application/x-mpegURL'
                }],
                metaTitle:"Video title", 
                metaSubtitle:"Video subtitle", 
                metaThumbnail:"/plugins/videojs8/examples/images/logo.png" 
            });
            */

            // Escucha los eventos prevenientes de la ventana padre que lo contiene
                
                window.addEventListener('message', onMessageReceived);

                function onMessageReceived(event) {

                    switch (event.data.event) {

                        case 'play':

                            player.play()

                        break;

                        case 'pause':

                            player.pause()

                        break;

                        case 'rewind':

                            player.rewind()

                        break;

                        case 'forward':

                            player.forward()

                        break;

                        case 'mute':

                            player.muted(true)

                        break;

                        case 'unmute':

                            player.muted(false)

                        break;

                        default:

                        break;

                    }

                }

            // Cuanta de consumo de ancho de banda

                let lastTime = new Date().getTime();
                let lastBytes = 0;

                player.on('progress', function(event) {
                  
                    console.log(player.buffered.end(0))

                });

        </script>

    </body>

</html>