<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - La Esquina del Sabor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background: #f5f1ea; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0;">
    <div style="display: flex; max-width: 900px; width: 100%; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        <div style="flex: 1; background: #e86a17; padding: 48px; color: #fff; display: flex; flex-direction: column; justify-content: center;">
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 36px; margin-bottom: 16px;">La Esquina<br>del Sabor</h1>
            <p style="font-size: 16px; opacity: 0.9;">Calor, orden y sabor: la cocina que nunca se detiene</p>
        </div>
        <div style="flex: 1; padding: 48px;">
            <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; margin-bottom: 8px;">Bienvenido</h2>
            <p style="color: #7a7066; margin-bottom: 32px;">Inicia sesión para continuar</p>
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Usuario</label>
                    <input type="text" name="username" value="{{ old('username') }}" required style="width: 100%; padding: 10px 14px; border: 1px solid #ddd4c7; border-radius: 8px; font-size: 15px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-weight: 600; margin-bottom: 6px; font-size: 14px;">Contraseña</label>
                    <input type="password" name="password" required style="width: 100%; padding: 10px 14px; border: 1px solid #ddd4c7; border-radius: 8px; font-size: 15px; box-sizing: border-box;">
                </div>
                <button type="submit" style="width: 100%; padding: 12px; background: #e86a17; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">Iniciar Sesión</button>
            </form>
        </div>
    </div>
    <script>
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({icon:'error', title:'Error de acceso', text:{!! json_encode(implode("\n", $errors->all())) !!}});
            });
        @endif
    </script>
</body>
</html>
