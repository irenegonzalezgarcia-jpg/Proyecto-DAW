// ==========================================================================
// 1. REFERENCIAS AL DOM (Utilizando tus IDs tradicionales)
// ==========================================================================
const formularioRegistro = document.getElementById("formRegistro");

const nombreUsuario = document.getElementById("nombreUsuario");
const apellidosUsuario = document.getElementById("apellidosUsuario");
const emailUsuario = document.getElementById("emailUsuario");
const passwordUsuario = document.getElementById("passwordUsuario");
const confirmPasswordUsuario = document.getElementById("confirmPasswordUsuario");
const telefonoUsuario = document.getElementById("telefonoUsuario");

// ==========================================================================
// 2. LOCALSTORAGE
// ==========================================================================
// Base de datos simulada de usuarios registrados en la plataforma
let usuarios = JSON.parse(localStorage.getItem("usuarios_registrados")) || [];
// 3. FUNCIONES LÓGICAS
function guardarUsuarios() {
  localStorage.setItem("usuarios_registrados", JSON.stringify(usuarios));
}

function limpiarCampos() {
  nombreUsuario.value = "";
  apellidosUsuario.value = "";
  emailUsuario.value = "";
  passwordUsuario.value = "";
  confirmPasswordUsuario.value = "";
  telefonoUsuario.value = "";
}

// 4. FUNCIONES DE INTERFAZ (UI)
function mostrarRegistroOk() {
  const divExito = document.createElement("div");
  divExito.style.backgroundColor = "#e6f4ea";
  divExito.style.color = "#137333";
  divExito.style.padding = "15px";
  divExito.style.marginTop = "15px";
  divExito.style.borderRadius = "4px";
  divExito.style.textAlign = "center";
  divExito.style.fontWeight = "bold";
  divExito.textContent = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
  
  formularioRegistro.append(divExito);
}

// 5. EVENTOS
if (formularioRegistro) {
  formularioRegistro.addEventListener("submit", (e) => {
    e.preventDefault();

    // Captura limpia de valores eliminando espacios huérfanos
    const nombre = nombreUsuario.value.trim();
    const apellidos = apellidosUsuario.value.trim();
    const email = emailUsuario.value.trim();
    const password = passwordUsuario.value;
    const confirmPassword = confirmPasswordUsuario.value;
    const telefono = telefonoUsuario.value.trim();

    // 5.1 Validación de campos obligatorios
    if (nombre === "" || apellidos === "" || email === "" || password === "" || confirmPassword === "") {
      alert("Por favor, rellene todos los campos obligatorios (*)");
      return;
    }

    // 5.2 Validación de formato de correo electrónico
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert("Por favor, introduce un email válido (ejemplo@dominio.com).");
      return;
    }

    // 5.3 Validación de control: Evitar correos duplicados
    const existeUsuario = usuarios.some(u => u.email === email);
    if (existeUsuario) {
      alert("Este correo electrónico ya está registrado. Intente iniciar sesión.");
      return;
    }

    // 5.4 Validación de seguridad: Concordancia de contraseñas
    if (password !== confirmPassword) {
      alert("Las contraseñas introducidas no coinciden.");
      return;
    }

    // Estructura de guardado del objeto usuario (esencia del curso)
    const nuevoUsuario = {
      id: crypto.randomUUID(),
      nombre: nombre,
      apellidos: apellidos,
      email: email,
      password: password, // Almacenamiento local educativo
      telefono: telefono,
      fecha_registro: new Date().toLocaleString()
    };

    // Operación lógica de inserción
    usuarios.push(nuevoUsuario);
    guardarUsuarios();
    limpiarCampos();
    mostrarRegistroOk();
  });
}

// 6. INICIALIZACIÓN
document.addEventListener("DOMContentLoaded", () => {
  console.log("Módulo de registro de usuarios inicializado correctamente.");
});