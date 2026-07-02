document.addEventListener('DOMContentLoaded', function () {

    var boton     = document.getElementById('pc-generar');
    var contenedor = document.getElementById('pc-colores');
    var aviso     = document.getElementById('pc-aviso');

    if (!boton) return;

    boton.addEventListener('click', function () {

        // aqui para motrar la carga, el estado
        contenedor.innerHTML = '<p class="pc-mensaje">Cargando colores...</p>';
        boton.disabled = true;
        boton.textContent = 'Cargando...';

        // de AJAX a WordPress
        var formData = new FormData();
        formData.append('action', 'pc_obtener_colores');
        formData.append('nonce', PC_Config.nonce);

        // Fetch al backend de WordPress
        //se llama a la api 
        fetch(PC_Config.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(function (respuesta) {
            return respuesta.json();
        })
        .then(function (datos) {

            boton.disabled = false;
            boton.textContent = 'Generar Paleta';

            if (!datos.success) {
                contenedor.innerHTML = '<p class="pc-mensaje">Hubo un error al obtener los colores.</p>';
                return;
            }

            // aqui se muesta los colores recibidos de la api
            contenedor.innerHTML = '';

            datos.data.forEach(function (color) {
                var item = document.createElement('div');
                item.className = 'pc-color-item';
                item.title = 'Clic para copiar ' + color.hex;

                item.innerHTML =
                    '<div class="pc-color-caja" style="background-color:' + color.hex + '"></div>' +
                    '<span class="pc-color-hex">' + color.hex + '</span>' +
                    '<span class="pc-color-nombre">' + color.nombre + '</span>';

                
                item.addEventListener('click', function () {
                    navigator.clipboard.writeText(color.hex).then(function () {
                        aviso.style.display = 'block';
                        setTimeout(function () {
                            aviso.style.display = 'none';
                        }, 2000);
                    });
                });

                contenedor.appendChild(item);
            });

        })
        .catch(function () {
            boton.disabled = false;
            boton.textContent = 'Generar Paleta';
            contenedor.innerHTML = '<p class="pc-mensaje">Error de conexión. Intentá de nuevo.</p>';
        });

    });

});
