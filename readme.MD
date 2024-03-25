
- Requiere tener queue de laravel configurado 
.env
`QUEUE_CONNECTION=database`

php artisan queue:table
php artisan migrate

php artisan queue:work

Configurar supervisor

Para instalar `ffmpeg` y `ffprobe` en un servidor Ubuntu y configurar sus rutas correctamente, puedes seguir los siguientes pasos. Esto te permitirá utilizar estas herramientas en tus proyectos alojados en el servidor, similar a cómo lo haces en tu entorno local con XAMPP.

### Paso 1: Instalar FFmpeg en Ubuntu

Primero, abre una terminal en tu servidor Ubuntu. Puedes conectarte a tu servidor mediante SSH si estás manejando un servidor remoto. Una vez conectado, ejecuta los siguientes comandos para instalar `ffmpeg`:

```bash
sudo apt update
sudo apt install ffmpeg
```

Este proceso instalará `ffmpeg` junto con `ffprobe`, ya que ambos vienen incluidos en el paquete `ffmpeg`.

### Paso 2: Verificar la instalación

Para asegurarte de que `ffmpeg` y `ffprobe` se han instalado correctamente, puedes verificar sus versiones ejecutando:

```bash
ffmpeg -version
ffprobe -version
```

Estos comandos mostrarán las versiones de `ffmpeg` y `ffprobe`, respectivamente, confirmando que la instalación fue exitosa.

### Paso 3: Obtener las rutas de FFmpeg y FFprobe

Para usar `ffmpeg` y `ffprobe` en tu aplicación, necesitas saber sus rutas. Generalmente, al instalarlos mediante `apt`, se ubican en `/usr/bin/ffmpeg` y `/usr/bin/ffprobe`. Puedes verificar estas rutas utilizando el comando `which`:

```bash
which ffmpeg
which ffprobe
```

Estos comandos devolverán las rutas completas de `ffmpeg` y `ffprobe`.

### Paso 4: Configurar las rutas en tu aplicación

Ahora que tienes las rutas, puedes configurarlas en tu aplicación. Basándote en tu ejemplo local, deberías ajustar las rutas en la configuración para que apunten a las ubicaciones correctas en Ubuntu. Por ejemplo:

```php
'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),

'ffprobe_path' => env('FFMPEG_PATH', '/usr/bin/ffprobe'),
```

Asegúrate de reemplazar `/usr/bin/ffmpeg` y `/usr/bin/ffprobe` con las rutas correctas si difieren en tu servidor.

### Nota

Es importante mantener las buenas prácticas de seguridad y gestión de configuraciones. Utilizar variables de entorno, como lo haces con `env('FFMPEG_PATH', 'ruta')`, es una excelente práctica para mantener tus configuraciones seguras y fácilmente modificables entre diferentes entornos (desarrollo, prueba, producción).

Con estos pasos, deberías ser capaz de instalar y configurar `ffmpeg` y `ffprobe` en tu servidor Ubuntu sin problemas.