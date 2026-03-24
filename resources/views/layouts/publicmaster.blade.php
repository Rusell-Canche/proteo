<!DOCTYPE html>
<html lang="en">

<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Incluir Bootstrap 4 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Incluir jQuery (necesario para el funcionamiento de Bootstrap 4) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <!-- Incluir los scripts de Bootstrap 4 -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/cropped-color1.png" type="image/x-icon">
    <title>@yield('title')</title> <!-- Si no se define un título específico en la vista, se usará "AGQROO" por defecto -->
    <style>
        .navbar-brand img {
            height: 40px;
        }
    </style>
</head>

<body>
    <div id="app">
        <app-navbar></app-navbar> <!-- Componente Vue para el Navbar -->

        <!-- Contenido principal -->
        <div class="container mt-3">
            @yield('content')
        </div>
    </div>
</body>

</html>