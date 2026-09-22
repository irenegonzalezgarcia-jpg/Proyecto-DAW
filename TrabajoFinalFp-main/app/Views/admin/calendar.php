<section class="section1" style="padding: 40px; text-align: left;">
    <h2>Panel de Administración</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div style="color: green; margin-bottom: 15px; font-weight: bold; padding: 10px; background-color: #e6f4ea; border-radius: 5px;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div style="color: red; margin-bottom: 15px; font-weight: bold; padding: 10px; background-color: #fce8e6; border-radius: 5px;">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- FORMULARIO MANUAL -->
        <div class="col-12" style="margin-bottom: 40px;">
            <h3>Grabar Cita Manualmente</h3>
            <div style="background-color: var(--accent-pastel); padding: 20px; border-radius: 8px;">
                <form action="/admin/appointments/add" method="POST" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Cliente</label>
                        <select name="user_id" id="selectorUsuario" required style="width:100%; padding:8px;">
                            <option value="">-- Seleccionar --</option>
                            <option value="new" style="font-weight:bold; background-color:#eee;">+ Nuevo Cliente</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Campos para nuevo cliente (ocultos por defecto) -->
                    <div id="camposNuevoCliente" style="display:none; width:100%; flex-wrap:wrap; gap:15px; background-color:#f9f9f9; padding:15px; border-radius:5px; margin-top:10px;">
                        <div style="flex: 1; min-width: 200px;">
                            <label style="font-weight:bold; display:block; margin-bottom:5px;">Nombre y apellidos*</label>
                            <input type="text" name="new_user_name" style="width:100%; padding:8px;" placeholder="Ej: María Pérez">
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label style="font-weight:bold; display:block; margin-bottom:5px;">Email*</label>
                            <input type="email" name="new_user_email" style="width:100%; padding:8px;" placeholder="Ej: maria@correo.com">
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label style="font-weight:bold; display:block; margin-bottom:5px;">Teléfono</label>
                            <input type="tel" name="new_user_phone" style="width:100%; padding:8px;" placeholder="Ej: 600123456">
                        </div>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px; margin-top:10px;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Tratamiento</label>
                        <select name="treatment_id" required style="width:100%; padding:8px;">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($treatments as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?> (<?= $t['duration_minutes'] ?> min)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex: 0 0 150px;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Fecha</label>
                        <input type="date" name="date" required style="width:100%; padding:8px;" min="<?= date('Y-m-d') ?>">
                    </div>
                    <div style="flex: 1; min-width: 100px;">
                        <label style="font-weight:bold; display:block; margin-bottom:5px;">Hora</label>
                        <select name="time" id="selectorHoraAdmin" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                            <option value="">Seleccione día primero</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn-inicio efecto" style="background-color: var(--color-dark); border:none; color:white; padding:10px 20px; cursor:pointer;">Programar Cita</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CALENDARIO INTERACTIVO -->
        <div class="col-12" style="margin-bottom: 40px;">
            <h3>Calendario de Citas</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                <!-- Grid del Calendario -->
                <div style="flex: 2; min-width: 300px; background-color: white; border: 1px solid #ccc; padding: 20px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <button id="btnMesAnterior" style="padding: 5px 10px; cursor: pointer;">&laquo; Anterior</button>
                        <h4 id="tituloCalendario" style="margin: 0; font-size: 1.2em;">Mes Año</h4>
                        <button id="btnMesSiguiente" style="padding: 5px 10px; cursor: pointer;">Siguiente &raquo;</button>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; text-align: center; font-weight: bold; margin-bottom: 10px;">
                        <div>L</div><div>M</div><div>X</div><div>J</div><div>V</div><div>S</div><div>D</div>
                    </div>
                    <div id="cuadriculaCalendario" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; text-align: center;">
                        <!-- Se llena con JS -->
                    </div>
                </div>

                <!-- Lista de Citas del Día -->
                <div style="flex: 1; min-width: 300px; background-color: var(--accent-pastel); padding: 20px; border-radius: 8px; max-height: 500px; overflow-y: auto; position: relative;">
                    <h4 id="tituloListaDia" style="margin-top: 0; border-bottom: 2px solid #333; padding-bottom: 10px; position: sticky; top: -20px; background-color: var(--accent-pastel); z-index: 10; padding-top: 20px; margin-left: -20px; margin-right: -20px; padding-left: 20px; padding-right: 20px;">Selecciona un día</h4>
                    <div id="listaCitasDia">
                        <p>Haz clic en un día del calendario para ver las citas.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
// Datos de citas agrupadas por fecha pasadas desde PHP
const datosCitas = <?= $appointmentsJson ?>;

let fechaActual = new Date(); // Mes y año actual para renderizar
let fechaSeleccionada = null;

const tituloCalendario = document.getElementById('tituloCalendario');
const cuadriculaCalendario = document.getElementById('cuadriculaCalendario');
const tituloListaDia = document.getElementById('tituloListaDia');
const listaCitasDia = document.getElementById('listaCitasDia');

function renderCalendar(date) {
    cuadriculaCalendario.innerHTML = '';
    const year = date.getFullYear();
    const month = date.getMonth();
    
    // Meses en español
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    tituloCalendario.textContent = `${monthNames[month]} ${year}`;
    
    // Calcular primer día del mes (0=Dom, 1=Lun)
    const firstDayIndex = new Date(year, month, 1).getDay();
    // Ajustar para que Lunes sea 0 y Domingo 6
    const startDay = firstDayIndex === 0 ? 6 : firstDayIndex - 1;
    
    // Días en el mes
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Rellenar espacios en blanco del principio
    for (let i = 0; i < startDay; i++) {
        const emptyCell = document.createElement('div');
        cuadriculaCalendario.appendChild(emptyCell);
    }
    
    // Crear días
    for (let i = 1; i <= daysInMonth; i++) {
        const cell = document.createElement('div');
        const mStr = String(month + 1).padStart(2, '0');
        const dStr = String(i).padStart(2, '0');
        const dateStr = `${year}-${mStr}-${dStr}`;
        
        cell.textContent = i;
        cell.style.padding = "10px";
        cell.style.border = "1px solid #eee";
        cell.style.cursor = "pointer";
        cell.style.borderRadius = "4px";
        cell.style.position = "relative";
        cell.style.display = "flex";
        cell.style.flexDirection = "column";
        cell.style.alignItems = "center";
        cell.style.minHeight = "50px";
        
        // Contenedor para los puntitos
        const dotsContainer = document.createElement('div');
        dotsContainer.style.display = "flex";
        dotsContainer.style.flexWrap = "wrap";
        dotsContainer.style.gap = "3px";
        dotsContainer.style.justifyContent = "center";
        dotsContainer.style.marginTop = "5px";
        
        // Comprobar si hay citas este día
        if (datosCitas[dateStr] && datosCitas[dateStr].length > 0) {
            cell.style.backgroundColor = "#f0f4f8";
            cell.style.fontWeight = "bold";
            
            // Añadir un puntito por cada cita
            datosCitas[dateStr].forEach(apt => {
                if (apt.status === 'cancelled') return; // No mostrar canceladas
                const dot = document.createElement('div');
                dot.style.width = "6px";
                dot.style.height = "6px";
                dot.style.borderRadius = "50%";
                // Rojo si es pendiente, Verde si es confirmada
                dot.style.backgroundColor = apt.status === 'pending' ? "#b71c1c" : "#2e7d32";
                dotsContainer.appendChild(dot);
            });
        }
        cell.appendChild(dotsContainer);
        
        // Resaltar día seleccionado
        if (dateStr === fechaSeleccionada) {
            cell.style.border = "2px solid #000";
        }
        
        // Evento click
        cell.addEventListener('click', () => {
            if (fechaSeleccionada === dateStr) {
                // Si ya estaba seleccionado, lo deseleccionamos para ver el mes entero
                fechaSeleccionada = null;
            } else {
                fechaSeleccionada = dateStr;
            }
            renderCalendar(fechaActual); // Re-render para mostrar/ocultar el borde seleccionado
            showAppointmentsList();
        });
        
        cuadriculaCalendario.appendChild(cell);
    }
    
    // Si no hay día seleccionado, mostrar citas del mes
    if (!fechaSeleccionada) {
        showAppointmentsList();
    }
}

function showAppointmentsList() {
    listaCitasDia.innerHTML = '';
    let aptsToShow = [];
    
    if (fechaSeleccionada) {
        // Citas de un día específico
        const parts = fechaSeleccionada.split('-');
        const formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
        tituloListaDia.textContent = `Citas: ${formattedDate}`;
        aptsToShow = datosCitas[fechaSeleccionada] || [];
    } else {
        // Citas de todo el mes actual visible
        tituloListaDia.textContent = `Citas del mes de ${tituloCalendario.textContent}`;
        const year = fechaActual.getFullYear();
        const month = fechaActual.getMonth() + 1;
        const prefix = `${year}-${String(month).padStart(2, '0')}`;
        
        for (const [date, apts] of Object.entries(datosCitas)) {
            if (date.startsWith(prefix)) {
                aptsToShow = aptsToShow.concat(apts);
            }
        }
        // Ordenar cronológicamente (ya vienen más o menos ordenadas por SQL, pero aseguramos)
        aptsToShow.sort((a, b) => {
            const dateA = new Date(a.appointment_date + 'T' + a.start_time);
            const dateB = new Date(b.appointment_date + 'T' + b.start_time);
            return dateA - dateB;
        });
    }
    
    if (aptsToShow.length === 0) {
        listaCitasDia.innerHTML = '<p>No hay citas programadas.</p>';
        return;
    }
    
    const ul = document.createElement('ul');
    ul.style.listStyle = 'none';
    ul.style.padding = '0';
    
    aptsToShow.forEach(apt => {
        const li = document.createElement('li');
        li.style.backgroundColor = "white";
        li.style.padding = "15px";
        li.style.marginBottom = "10px";
        li.style.borderRadius = "4px";
        li.style.boxShadow = "0 2px 4px rgba(0,0,0,0.05)";
        li.style.borderLeft = "4px solid var(--color-dark)";
        
        const coloresEstado = {
            'pending': { bg: '#dbe4ed', text: '#2c3e50', label: 'Pendiente' },
            'confirmed': { bg: '#d0d8cd', text: '#1f3822', label: 'Confirmada' },
            'cancelled': { bg: '#f2d6d6', text: '#6b2121', label: 'Cancelada' }
        };
        const infoEstado = coloresEstado[apt.status] || { bg: '#ddd', text: '#000', label: apt.status };

        // Formatear la fecha para mostrarla en la tarjeta (sobre todo útil en la vista de mes)
        const parts = apt.appointment_date.split('-');
        const cardDate = `${parts[2]}/${parts[1]}/${parts[0]}`;

        // Generar opciones de hora para el select de posponer
        let timeOptions = '';
        for (let h = 9; h < 20; h++) {
            ['00', '15', '30', '45'].forEach(m => {
                const timeStr = String(h).padStart(2, '0') + ':' + m;
                const isSelected = apt.start_time && apt.start_time.startsWith(timeStr) ? 'selected' : '';
                timeOptions += `<option value="${timeStr}" ${isSelected}>${timeStr}</option>`;
            });
        }

        li.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:5px;">
                <div style="font-weight:bold; color:var(--color-dark);">
                    ${!fechaSeleccionada ? `<span style="color:#666; font-size:0.9em; margin-right:5px;">${cardDate}</span> ` : ''}
                    ${apt.start_time} - ${apt.end_time}
                </div>
                <div style="padding: 3px 8px; border-radius: 4px; background-color: ${infoEstado.bg}; font-size: 0.8em; color: ${infoEstado.text}; font-weight: bold;">
                    ${infoEstado.label}
                </div>
            </div>
            <div style="font-weight:bold;">${apt.user_name}</div>
            <div style="color:#555; font-size:0.9em; margin-bottom:10px;">${apt.treatment_name}</div>
            <div style="display:flex; gap:10px; margin-top: 10px;">
                ${apt.status === 'pending' ? `<form action="/admin/appointments/status" method="POST" style="margin:0;"><input type="hidden" name="id" value="${apt.id}"><input type="hidden" name="status" value="confirmed"><button type="submit" style="background-color:#d0d8cd; border:1px solid #1f3822; color:#1f3822; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:0.85em; font-weight:bold;">✔ Confirmar</button></form>` : ''}
                ${apt.status !== 'cancelled' ? `<form action="/admin/appointments/status" method="POST" style="margin:0;"><input type="hidden" name="id" value="${apt.id}"><input type="hidden" name="status" value="cancelled"><button type="submit" style="background-color:#f2d6d6; border:1px solid #6b2121; color:#6b2121; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:0.85em; font-weight:bold;">✖ Cancelar</button></form>` : ''}
                ${apt.status !== 'cancelled' ? `<button onclick="document.getElementById('posponer-${apt.id}').style.display='flex'" style="background-color:#fff3cd; border:1px solid #856404; color:#856404; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:0.85em; font-weight:bold;">⏱ Posponer</button>` : ''}
            </div>
            
            <form id="posponer-${apt.id}" action="/admin/appointments/reschedule" method="POST" style="display:none; gap:5px; margin-top:10px; background:#f9f9f9; padding:10px; border-radius:5px; border:1px dashed #ccc; align-items:center;">
                <input type="hidden" name="id" value="${apt.id}">
                <input type="date" name="new_date" value="${apt.appointment_date}" required style="padding:4px; font-size:0.85em; max-width:110px;">
                <select name="new_time" required style="padding:4px; font-size:0.85em; max-width:80px;">
                    ${timeOptions}
                </select>
                <button type="submit" style="background-color:var(--color-dark); color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; font-size:0.85em;">Guardar</button>
            </form>
        `;
        ul.appendChild(li);
    });
    
    listaCitasDia.appendChild(ul);
}

// Navegación de meses
document.getElementById('btnMesAnterior').addEventListener('click', () => {
    fechaActual.setMonth(fechaActual.getMonth() - 1);
    renderCalendar(fechaActual);
});

document.getElementById('btnMesSiguiente').addEventListener('click', () => {
    fechaActual.setMonth(fechaActual.getMonth() + 1);
    renderCalendar(fechaActual);
});

// Mostrar/Ocultar campos de nuevo cliente
document.getElementById('selectorUsuario').addEventListener('change', function() {
    const camposNuevoCliente = document.getElementById('camposNuevoCliente');
    const inputs = camposNuevoCliente.querySelectorAll('input');
    
    if (this.value === 'new') {
        camposNuevoCliente.style.display = 'flex';
        inputs.forEach(input => {
            if (input.name !== 'new_user_phone') input.required = true;
        });
    } else {
        camposNuevoCliente.style.display = 'none';
        inputs.forEach(input => {
            input.required = false;
        });
    }
});

// Lógica para actualizar las horas disponibles y validar en Admin
const formAdminCita = document.querySelector('form[action="/admin/appointments/add"]');
if (formAdminCita) {
    const dateInput = formAdminCita.querySelector('input[name="date"]');
    const timeSelect = formAdminCita.querySelector('select[name="time"]');
    const treatmentSelect = formAdminCita.querySelector('select[name="treatment_id"]');

    function updateAdminAvailableHours() {
        const dateStr = dateInput.value;
        const treatmentId = treatmentSelect.value;
        
        timeSelect.innerHTML = '<option value="">Cargando horas...</option>';
        
        if (!dateStr || !treatmentId) {
            timeSelect.innerHTML = '<option value="">Seleccione día y tratamiento</option>';
            return;
        }

        const d = new Date(dateStr);
        const day = d.getDay(); // 0 = Domingo
        
        if (day === 0) {
            Swal.fire({title: 'Atención', text: 'El centro está cerrado los domingos.', icon: 'warning', confirmButtonColor: 'var(--color-dark)'});
            timeSelect.innerHTML = '<option value="">Cerrado</option>';
            return;
        }
        
        fetch(`/api/available-hours?date=${dateStr}&treatment_id=${treatmentId}`)
            .then(response => response.json())
            .then(hours => {
                timeSelect.innerHTML = '<option value="">-- Seleccionar --</option>';
                if (hours.length === 0) {
                    timeSelect.innerHTML += '<option value="" disabled>No hay horas libres</option>';
                } else {
                    hours.forEach(timeStr => {
                        timeSelect.innerHTML += `<option value="${timeStr}">${timeStr}</option>`;
                    });
                }
            })
            .catch(err => {
                console.error("Error cargando horas:", err);
                timeSelect.innerHTML = '<option value="">Error al cargar horas</option>';
            });
    }

    dateInput.addEventListener('change', updateAdminAvailableHours);
    treatmentSelect.addEventListener('change', updateAdminAvailableHours);

    formAdminCita.addEventListener('submit', function(e) {
        const dateStr = dateInput.value;
        const timeStr = timeSelect.value;
        
        if (dateStr) {
            const d = new Date(dateStr);
            if (d.getDay() === 0) {
                e.preventDefault();
                Swal.fire({title: 'No permitido', text: 'No se puede programar citas: El centro está cerrado los domingos.', icon: 'error', confirmButtonColor: 'var(--color-dark)'});
                return;
            }
        }
        if (!timeStr) {
            e.preventDefault();
            Swal.fire({title: 'Faltan datos', text: 'Por favor, selecciona una hora válida.', icon: 'warning', confirmButtonColor: 'var(--color-dark)'});
        }
    });
}

// Inicializar
renderCalendar(fechaActual);
</script>
