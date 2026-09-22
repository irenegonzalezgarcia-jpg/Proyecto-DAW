// ==========================================================================
// 1. REFERENCIAS AL DOM (Utilizando tus IDs tradicionales)
// ==========================================================================
const formularioLogin = document.getElementById("formLogin");

const emailLogin = document.getElementById("emailLogin");
const passwordLogin = document.getElementById("passwordLogin");

// ==========================================================================
// 2. LOCALSTORAGE
// ==========================================================================
// Traemos la lista de usuarios que se han registrado previamente en la web
let usuarios = JSON.parse(localStorage.getItem("usuarios_registrados")) || [];

// 3. FUNCIONES LÓGICAS
// Comprueba si las credenciales coinciden con algún usuario de la base de datos
function validarCredenciales(email, password) {
  // Buscamos un usuario cuyo email y contraseña coincidan a la vez
  return usuarios.find(u => u.email === email && u.password === password);
}

function guardarSesionActiva(usuario) {
  // Guardamos el usuario que ha entrado para saber quién está navegando
  localStorage.setItem("usuario_autenticado", JSON.stringify(usuario));
}

// 4. FUNCIONES DE INTERFAZ (UI)
function mostrarLoginOk(nombreUsuario) {
  const divExito = document.createElement("div");
  divExito.style.backgroundColor = "#e6f4ea";
  divExito.style.color = "#137333";
  divExito.style.padding = "15px";
  divExito.style.marginTop = "15px";
  divExito.style.borderRadius = "4px";
  divExito.style.textAlign = "center";
  divExito.style.fontWeight = "bold";
  divExito.textContent = `¡Bienvenido/a, ${nombreUsuario}! Redirigiendo...`;
  
  formularioLogin.append(divExito);
}

// 5. EVENTOS
if (formularioLogin) {
  formularioLogin.addEventListener("submit", (e) => {
    e.preventDefault();

    const email = emailLogin.value.trim();
    const password = passwordLogin.value;

    // 5.1 Validación de campos vacíos
    if (email === "" || password === "") {
      alert("Por favor, introduce tu email y contraseña.");
      return;
    }

    // 5.2 Validación lógica en LocalStorage
    const usuarioEncontrado = validarCredenciales(email, password);

    if (usuarioEncontrado) {
      // Si existe, iniciamos su sesión y avisamos en pantalla
      guardarSesionActiva(usuarioEncontrado);
      mostrarLoginOk(usuarioEncontrado.nombre);

      // Simulamos una pequeña pausa de 1.5 segundos para que se vea el mensaje de éxito antes de cambiar de página
      setTimeout(() => {
        window.location.href = "index.html"; // Lo mandamos a la home ya logueado
      }, 1500);

    } else {
      // Si no coincide el correo o la clave, lanzamos error de seguridad sutil
      alert("El correo electrónico o la contraseña no son correctos.");
    }
  });
}

// 6. INICIALIZACIÓN
document.addEventListener("DOMContentLoaded", () => {
  console.log("Módulo de inicio de sesión (Login) inicializado correctamente.");
});