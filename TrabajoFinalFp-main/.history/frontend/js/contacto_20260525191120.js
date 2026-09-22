// ==========================================================================
// 1. REFERENCIAS AL DOM (Utilizando IDs tradicionales como en el curso)
// ==========================================================================
const formularioContacto = document.querySelector(".form-contacto");

const nombreUsuario = document.getElementById("nombreUsuario");
const emailUsuario = document.getElementById("emailUsuario");
const telefonoUsuario = document.getElementById("telefonoUsuario");
const mensajeUsuario = document.getElementById("mensajeUsuario");

// ==========================================================================
// 2. LOCALSTORAGE
// ==========================================================================
let mensajes = JSON.parse(localStorage.getItem("mensajes_contacto")) || [];

// ==========================================================================
// 3. FUNCIONES LÓGICAS
// ==========================================================================
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

    // Recogemos los valores directamente desde tus referencias por ID
    const nombre = nombreUsuario.value.trim();
    const email = emailUsuario.value.trim();
    const telefono = telefonoUsuario.value.trim();

    // Validación de campos obligatorios
    if(nombre === "" || email === "" || telefono === ""){
      alert("Por favor, rellene todos los campos obligatorios (*)");
      return;
    }

    // Validación de formato de email
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
// Renderiza o prepara el entorno una vez que el HTML está completamente cargado
document.addEventListener("DOMContentLoaded", () => {
  // Dejamos el bloque listo por consistencia metodológica con el resto del proyecto
  console.log("Módulo de contacto inicializado correctamente.");
});