document.addEventListener("DOMContentLoaded", () => {
    gestionarFormularioContacto();
});

/**
 * Controla la validación y el comportamiento interactivo del formulario de contacto
 */
function gestionarFormularioContacto() {
    const formulario = document.querySelector(".form-contacto") || document.querySelector("form");
    if (!formulario) return;

    // Crear un contenedor para el mensaje de éxito oculto en el HTML dinámicamente
    const mensajeExito = document.createElement("div");
    mensajeExito.style.display = "none";
    mensajeExito.style.backgroundColor = "#e6f4ea";
    mensajeExito.style.color = "#137333";
    mensajeExito.style.padding = "15px";
    mensajeExito.style.marginTop = "15px";
    mensajeExito.style.borderRadius = "4px";
    mensajeExito.style.textAlign = "center";
    mensajeExito.style.fontWeight = "bold";
    mensajeExito.innerText = "¡Muchas gracias! Su mensaje ha sido enviado con éxito. Nos pondremos en contacto con usted en menos de 24 horas.";
    formulario.appendChild(mensajeExito);

    formulario.addEventListener("submit", (event) => {
        // Evitamos que la página se recargue o intente buscar un servidor inexistente
        event.preventDefault();

        // Captura de los elementos del formulario
        const inputNombre = formulario.querySelector('input[type="text"]');
        const inputEmail = formulario.querySelector('input[type="email"]');
        const inputTelefono = formulario.querySelector('input[type="tel"]') || formulario.querySelectorAll('input')[2];
        const txtMensaje = formulario.querySelector("textarea");

        const nombre = inputNombre ? inputNombre.value.trim() : "";
        const email = inputEmail ? inputEmail.value.trim() : "";
        const telefono = inputTelefono ? inputTelefono.value.trim() : "";

        // 1. Validación de Campos Vacíos Obligatorios
        if (nombre === "" || email === "" || telefono === "") {
            alert("Por favor, rellene todos los campos marcados como obligatorios.");
            return;
        }

        // 2. Validación Avanzada de Formato de Email mediante Expresión Regular
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("La dirección de correo electrónico introducida no tiene un formato válido (ejemplo@dominio.com).");
            if (inputEmail) inputEmail.focus();
            return;
        }

        // 3. Flujo de éxito (Feedback Visual)
        // Ocultamos el botón de enviar o el propio formulario para simular la transición
        const botonEnviar = formulario.querySelector('input[type="submit"]') || formulario.querySelector('button');
        if (botonEnviar) botonEnviar.style.display = "none";

        // Mostramos el mensaje de confirmación que preparamos
        mensajeExito.style.display = "block";

        // Opcional: Limpiar los campos del formulario tras 2 segundos
        setTimeout(() => {
            formulario.reset();
        }, 1000);
    });
}