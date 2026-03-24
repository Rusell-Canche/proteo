<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="assets/tridente2.png" type="image/x-icon">

        <title>Bienvenido</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
.welcome-page {
    min-height: calc(100vh - 4rem); /* Ajusta la altura para centrar verticalmente */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.btn-primary {
    padding: 0.75rem 2rem;
    background-color: #530710; /* Color de fondo del botón */
    color: white; /* Color del texto del botón */
    border-radius: 0.375rem; /* Borde redondeado */
    text-decoration: none; /* Quita la subrayado por defecto */
    font-size: 1.125rem; /* Tamaño de fuente */
    font-weight: bold; /* Negrita */
    cursor: pointer;
    transition: background-color 0.3s ease; /* Transición suave */
}

.btn-primary:hover {
    background-color: #bb1616; /* Cambia el color de fondo al pasar el ratón */
}

.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    
}
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
        </style>
    </head>
    <body class="antialiased">
    <div class="welcome-page bg-dots-darker">
        <h1 class="text-center text-4xl font-bold mb-8">Bienvenido al Archivo General del Estado de Quintana Roo</h1>
        <p class="text-center text-lg mb-8">Por favor inicia sesión para comenzar.</p>
        <div class="flex justify-center"><br>
            <a href="/login" class="btn-primary">Iniciar sesión</a>
        </div>
    </div>
    </body>
</html>
