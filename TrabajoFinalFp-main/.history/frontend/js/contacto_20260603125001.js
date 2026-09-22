// 1. REFERENCIAS AL DOM (Utilizando IDs tradicionales como en el curso)
const formularioContacto = document.querySelector(".form-contacto");

const nombreUsuario = document.getElementById("nombreUsuario");
const emailUsuario = document.getElementById("emailUsuario");
const telefonoUsuario = document.getElementById("telefonoUsuario");
const mensajeUsuario = document.getElementById("mensajeUsuario");

// 2. LOCALSTORAGE
let mensajes = JSON.parse(localStorage.getItem("mensajes_contacto")) || [];

// 3. FUNCIONES LÓGICAS
function guardarMensajes(){
  localStorage.setItem("mensajes_contacto", JSON.stringify(mensajes));
}

function limpiarCampos(){
  nombreUsuario.value = "";
  emailUsuario.value = "";
  telefonoUsuario.value = "";
  mensajeUsuario.value = "";
}

// 4. FUNCIONES DE INTERFAZ (UI)
function mostrarMensajeExito(){
  const divExito = document.createElement("div");
  divExito.style.backgroundColor = "#e6f4ea";
  divExito.style.color = "#137333";
  divExito.style.padding = "15px";
  divExito.style.marginTop = "15px";
  divExito.style.borderRadius = "4px";
  divExito.style.textAlign = "center";
  divExito.style.fontWeight = "bold";
  divExito.textContent = "¡Muchas gracias! Tu mensaje ha sido enviado con éxito.";
  
  formularioContacto.append(divExito);
}

// 5. EVENTOS
if (formularioContacto) {
  formularioContacto.addEventListener("submit", (e)=>{
    e.preventDefault();

    // 🌟 CAMBIO AQUÍ: Validación nativa HTML5
    // Si algún campo obligatorio está vacío, activa el bocadillo "Completa este campo"
    if (!formularioContacto.checkValidity()) {
      formularioContacto.reportValidity();
      return; // Detiene la ejecución aquí para que no guarde nada vacío
    }

    // Recogemos los valores directamente desde tus referencias por ID si todo es válido
    const nombre = nombreUsuario.value.trim();
    const email = emailUsuario.value.trim();
    const telefono = telefonoUsuario.value.trim();

    // Validación de formato de email (mantenemos el alert solo si el formato falla)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email)){
      alert("Por favor, introduce un email válido (ejemplo@dominio.com).");
      return;
    }

    // Estructura del objeto final que se guarda en el LocalStorage
    const nuevoMensaje = {
      id: crypto.randomUUID(),
      nombre: nombre,
      email: email,
      telefono: telefono,
      comentarios: mensajeUsuario.value.trim(),
      fecha: new Date().toLocaleString()
    };

    mensajes.push(nuevoMensaje);
    guardarMensajes();
    limpiarCampos();
    mostrarMensajeExito();
  });
}

// 6. INICIALIZACIÓN
document.addEventListener("DOMContentLoaded", () => {
  console.log("Módulo de contacto inicializado correctamente.");
});