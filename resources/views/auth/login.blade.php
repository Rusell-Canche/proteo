<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/tridente2.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
        <!-- Logo -->
        <div class="mb-8">
            <img src="{{ asset('assets/logo.png') }}" alt="Your Logo" width="400" height="100">
        </div>

        <!-- Formulario de inicio de sesión -->
        <form id="loginForm" action="{{ route('users.login') }}" method="POST" class="px-6 py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg w-full sm:max-w-md">
            @csrf
            <!-- Correo electrónico -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                <input id="email" class="p-2 mt-2 rounded-xl border w-full focus:border-indigo-500" type="email" name="email" placeholder="Correo Electrónico" required autofocus autocomplete="username">
                @error('email')
                    <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mt-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                <input id="password" class="p-2 mt-2 rounded-xl border w-full focus:border-indigo-500" type="password" name="password" placeholder="Contraseña" required autocomplete="current-password">
                <button type="button" class="absolute inset-y-0 right-0 px-4 py-9" onclick="togglePasswordVisibility()">
                    <i id="togglePasswordIcon" class="fas fa-eye-slash text-gray-400 hover:text-red-900"></i>
                </button>
                @error('password')
                    <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <!-- Recordar usuario -->
            <div class="block mt-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded text-indigo-600 focus:ring-indigo-500 dark:text-indigo-400">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Recuérdame</span>
                </label>
            </div>

            <!-- Enlaces adicionales -->
            <div class="flex items-center justify-end mt-4">
                <!-- Enlace para restablecer contraseña -->
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium hover:text-red-900">¿Olvidaste tu contraseña?</a>
                @endif
                
                <!-- Botón de inicio de sesión -->
                <button type="submit" class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-900 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-700">Iniciar sesión</button>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var passwordIcon = document.getElementById("togglePasswordIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.classList.remove("fa-eye-slash");
                passwordIcon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                passwordIcon.classList.remove("fa-eye");
                passwordIcon.classList.add("fa-eye-slash");
            }
        }
    </script>
</body>
</html>
