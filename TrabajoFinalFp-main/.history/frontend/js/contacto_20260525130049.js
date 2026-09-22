// 1. REFERENCIAS AL DOM
const formContacto = document.querySelector(".form-contacto");
// Capturamos los inputs por su atributo name que ya tienes en tu HTML
const inputNombre = document.querySelector('input[name="nombre"]');
const inputMail = document.querySelector('input[name="mail"]');
const inputTelefono = document.querySelector('input[name="telefono"]');
const txtComentarios = document.querySelector('textarea[name="comentarios"]');
const contenedorForm = document.querySelector(".segundo.col-6");

// 2. LOCALSTORAGE
// Guardamos los mensajes recibidos para que los lea el admin
let mensajes = JSON.parse(localStorage.getItem("mensajes_contacto")) || [];

// 3. FUNCIONES LÓGICAS
function guardarMensajes(){
  localStorage.setItem("mensajes_contacto", JSON.stringify(mensajes));
}

function limpiarCampos(){
  inputNombre.value = "";
  inputMail.value = "";
  inputTelefono.value = "";
  txtComentarios.value = "";
}

// 4. FUNCIONES DE INTERFAZ (UI)
function mostrarMensajeExito(){
  // Creamos el bloque de éxito de forma dinámica con tu metodología
  const divExito = document.createElement("div");
  divExito.style.backgroundColor = "#e6f4ea";
  divExito.style.color = "#137333";
  divExito.style.padding = "15px";
  divExito.style.marginTop = "15px";
  divExito.style.borderRadius = "4px";
  divExito.style.textAlign = "center";
  divExito.style.fontWeight = "bold";
  divExito.textContent = "¡Muchas gracias! Tu mensaje ha sido enviado. El administrador lo revisará en el panel.";
  
  contenedorForm.append(divExito);
}

// 5. EVENTOS
formContacto.addEventListener("submit", (e)=>{
  e.preventDefault();

  const nombre = inputNombre.value.trim();
  const email = inputMail.value.trim();
  const telefono = inputTelefono.value.trim();

  // Validación de campos obligatorios
  if(nombre === "" || email === "" || telefono === ""){
    alert("Por favor, rellene todos los campos obligatorios (*)");
    return;
  }

  // Validación de formato de email con Regex
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if(!emailRegex.test(email)){
    alert("Por favor, introduce un email válido.");
    return;
  }

  // Estructura del objeto mensaje (siguiendo tu estilo de cuentas)
  const nuevoMensaje = {
    id: crypto.randomUUID(),
    nombre: nombre,
    email: email,
    telefono: telefono,
    comentarios: txtComentarios.value.trim(),
    fecha: new Date().toLocaleString()
  };

  mensajes.push(nuevoMensaje);
  guardarMensajes();
  limpiarCampos();
  mostrarMensajeExito();
});