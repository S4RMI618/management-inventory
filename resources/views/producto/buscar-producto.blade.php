<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Búsqueda de Productos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; }
        .resultado { margin-top: 2rem; padding: 1rem; border: 1px solid #ccc; border-radius: 5px; }
        .error { color: red; margin-top: 1rem; }
    </style>
</head>
<body>
    <h1>Búsqueda de Productos</h1>
    <form id="form-busqueda">
        <label for="query">Código, Lote o Serie:</label>
        <input type="text" id="query" name="query" required>
        <button type="submit">Buscar</button>
    </form>

    <div class="resultado" id="resultado" style="display: none;"></div>
    <div class="error" id="error" style="display: none;"></div>

    <script>
        document.getElementById('form-busqueda').addEventListener('submit', function (e) {
            e.preventDefault();
            const query = document.getElementById('query').value;
            const resultadoDiv = document.getElementById('resultado');
            const errorDiv = document.getElementById('error');
            resultadoDiv.style.display = 'none';
            errorDiv.style.display = 'none';

            fetch(`/api/buscar-producto?query=${encodeURIComponent(query)}`)
                .then(async res => {
                    if (!res.ok) {
                        const err = await res.json();
                        throw new Error(err.message || 'Error de búsqueda');
                    }
                    return res.json();
                })
                .then(data => {
                    resultadoDiv.style.display = 'block';
                    resultadoDiv.innerHTML = `
                        <strong>Tipo de coincidencia:</strong> ${data.match_type}<br>
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    `;
                })
                .catch(err => {
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = err.message;
                });
        });
    </script>
</body>
</html>