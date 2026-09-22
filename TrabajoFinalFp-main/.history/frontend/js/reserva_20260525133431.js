// ==========================================================================
// 1. REFERENCIAS AL DOM (Utilizando IDs tradicionales)
// ==========================================================================
const formularioReserva = document.getElementById("formReserva");

const nombreReserva = document.getElementById("nombreReserva");
const emailReserva = document.getElementById("emailReserva");
const telefonoReserva = document.getElementById("telefonoReserva");
const selectServicio = document.getElementById("selectServicio");
const fechaCita = document.getElementById("fechaCita");
const mensajeReserva = document.getElementById("mensajeReserva");

// ==========================================================================
// 2. LOCALSTORAGE
// ==========================================================================
let citas = JSON.parse(localStorage.getItem("citas_reservadas")) || [];

// ==========================================================================
// 3. FUNCIONES LÓGICAS
// ==========================================================================
function guardarCitas() {
  localStorage.setItem("citas_reservadas", JSON.stringify(citas));
}

function limpiarCampos() {
  nombreReserva.value = "";
  emailReserva.value = "";
  telefonoReserva.value = "";
  selectServicio.value = "";
  fechaCita.value = "";
  mensajeReserva.value = "";
}

function configurarFechaMinima() {
  if (!fechaCita) return;
  
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  let mm = hoy.getMonth() + 1;
  let dd = hoy.getDate();

  // Forzamos formato de dos dígitos para el mes y el día
  if (mm < 10) mm = '0' + mm;
  if (dd < 10) dd = '0' + dd;

  fechaCita.min = `${yyyy}-${mm}-${dd}`;
}

// ==========================================================================
// 4. FUNCIONES DE INTERFAZ (UI)
// ==========================================================================
function mostrarReservaOk() {
  const divExito = document.createElement("div");
  divExito.style.backgroundColor = "#e6f4ea";
  divExito.style.color = "#137333";
  divExito.style.padding = "15px";
  divExito.style.marginTop = "15px";
  divExito.style.borderRadius = "4px";
  divExito.style.textAlign = "center";
  divExito.style.fontWeight = "bold";
  divExito.style.fontFamily = "'Lato', sans-serif";
  divExito.textContent = "¡Solicitud recibida! Te llamaremos en menos de 24 horas para confirmar tu hora exacta.";
  
  formularioReserva.append(divExito);
}

// ==========================================================================
// 5. EVENTOS
// ==========================================================================
if (formularioReserva) {
  formularioReserva.addEventListener("submit", (e) => {
    e.preventDefault();

    const nombre = nombreReserva.value.trim();
    const email = emailReserva.value.trim();
    const telefono = telefonoReserva.value.trim();
    const servicio = selectServicio.value;
    const fecha = fechaCita.value;

    // Validación manual de campos obligatorios
    if (nombre === "" || email === "" || telefono === "" || servicio === "" || fecha === "") {
      alert("Por favor, rellene todos los campos obligatorios (*)");
      return;
    }

    // Validación estricta del formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert("Por favor, introduce un email válido (ejemplo@dominio.com).");
      return;
    }

    // Estructura de objeto coherente con tu lógica de cuentas bancarias
    const nuevaCita = {
      id: crypto.randomUUID(),
      titular: nombre,
      email: email,
      telefono: telefono,
      servicio: servicio,
      fecha_cita: fecha,
      notas: mensajeReserva.value.trim(),
      fecha_solicitud: new Date().toLocaleString()
    };

    citas.push(nuevaCita);
    guardarCitas();
    limpiarCampos();
    mostrarReservaOk();
  });
}

// ==========================================================================
// 6. INICIALIZACIÓN
// ==========================================================================
document.addEventListener("DOMContentLoaded", () => {
  configurarFechaMinima();
});