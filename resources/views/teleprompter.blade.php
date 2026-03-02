<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROMPTER: {{ $rundown->show->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilo específico para lectura de prompter */
        .prompter-text {
            line-height: 1.4;
            text-transform: uppercase; /* Opcional: muchos presentadores prefieren mayúsculas */
        }
        /* Ocultar scrollbar para limpieza visual */
        body::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-black text-white px-20 py-10 overflow-y-scroll">

    <div class="max-w-5xl mx-auto">
        @foreach($rundown->segments as $segment)
            <div class="mb-32">
                <div class="text-blue-500 text-2xl font-bold mb-4 border-b border-blue-900 uppercase">
                    {{ $segment->order_index }}. {{ $segment->title }}
                </div>
                
                <div class="prompter-text text-7xl font-bold">
                    {!! nl2br(e($segment->script_content)) !!}
                </div>
            </div>
        @endforeach

        <div class="text-gray-700 text-center py-20 text-4xl italic">
            *** FIN DEL PROGRAMA ***
        </div>
    </div>

    <script>
        window.addEventListener('keydown', (e) => {
            if (e.key === ' ') { // Barra espaciadora para bajar suavemente
                e.preventDefault();
                window.scrollBy({ top: 100, behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>