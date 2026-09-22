<section class="section2">
    <div class="row">

        <div class="primero col-6">
            <h3>RESERVA TU MOMENTO</h3>
            <p>En <strong>Inspire Beauty</strong> queremos ofrecerte una experiencia única y personalizada desde
                el primer instante. Gestiona tu solicitud de cita previa de forma online en menos de un minuto.
            </p>

            <h4>INFORMACIÓN IMPORTANTE:</h4>
            <ul class="lista-condiciones">
                <li>🕒 <strong>Confirmación:</strong> Tras recibir tu solicitud, nuestro equipo se pondrá en
                    contacto contigo para confirmar la hora exacta de tu sesión.</li>
                <li>❌ <strong>Cancelaciones:</strong> Si no puedes asistir, por favor avísanos con un mínimo de
                    24 horas de antelación para liberar la cabina.</li>
                <li>📍 <strong>Puntualidad:</strong> Recomendamos llegar 5 minutos antes para garantizar la
                    duración completa de tu tratamiento.</li>
            </ul>

            <h4>NUESTRO HORARIO DE ATENCIÓN:</h4>
            <div class="grid-horario">
                <p><strong>Lunes a Viernes:</strong> 09:00h - 20:00h</p>
                <p><strong>Sábados:</strong> 10:00h - 14:00h</p>
                <p><strong>Domingos:</strong> Cerrado</p>
            </div>

            <h1 class="marca-agua">Inspire Beauty</h1>
        </div>

        <div class="segundo col-6">
            <h4>SOLICITA TU CITA:</h4>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div style="color: red; margin-bottom: 15px; font-weight: bold;">
                    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div style="color: green; margin-bottom: 15px; font-weight: bold;">
                    <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form class="form-contacto" id="formReserva" action="/reservar" method="POST" novalidate>
                <input type="text" id="nombreReserva" name="nombre" placeholder="Nombre y apellidos*" required value="<?= htmlspecialchars($oldInput['nombre'] ?? '') ?>">
                <input type="email" id="emailReserva" name="email" placeholder="Email*" required value="<?= htmlspecialchars($oldInput['email'] ?? '') ?>">
                <input type="tel" id="telefonoReserva" name="telefono" placeholder="Teléfono*" required value="<?= htmlspecialchars($oldInput['telefono'] ?? '') ?>">

                <!-- Tratamientos cargados dinámicamente desde BD -->
                <select id="selectServicio" name="treatment_id" required>
                    <option value="" disabled <?= empty($oldInput['treatment_id']) ? 'selected' : '' ?>>Selecciona un tratamiento*</option>
                    <?php foreach ($treatments as $treatment): ?>
                        <option value="<?= htmlspecialchars($treatment['id']) ?>" <?= ($oldInput['treatment_id'] ?? '') == $treatment['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($treatment['name']) ?> (<?= htmlspecialchars($treatment['duration_minutes']) ?> min)
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="date" id="fechaCita" name="fecha" required value="<?= htmlspecialchars($oldInput['fecha'] ?? '') ?>">
                <!-- Selector de hora dinámico -->
                <select id="horaCita" name="hora" required data-old-time="<?= htmlspecialchars($oldInput['hora'] ?? '') ?>">
                    <option value="">Selecciona fecha y tratamiento primero</option>
                </select>

                <textarea id="mensajeReserva" name="comentarios"
                    placeholder="Notas adicionales o preferencias médicas..."><?= htmlspecialchars($oldInput['comentarios'] ?? '') ?></textarea>

                <button type="submit" class="btn-enviar">Solicitar Cita</button>
            </form>
            <p>*Campos obligatorios</p>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('fechaCita');
    const timeSelect = document.getElementById('horaCita');
    const formReserva = document.getElementById('formReserva');
    const selectServicio = document.getElementById('selectServicio');

    if (dateInput && timeSelect && selectServicio) {
        // Bloquear fechas anteriores a hoy
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;

        function updateAvailableHours() {
            const dateStr = dateInput.value;
            const treatmentId = selectServicio.value;
            
            timeSelect.innerHTML = '<option value="">Cargando horas...</option>';
            
            if (!dateStr || !treatmentId) {
                timeSelect.innerHTML = '<option value="">Selecciona fecha y tratamiento</option>';
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
                    timeSelect.innerHTML = '<option value="">Selecciona hora</option>';
                    if (hours.length === 0) {
                        timeSelect.innerHTML += '<option value="" disabled>No hay horas libres</option>';
                    } else {
                        const oldTime = timeSelect.getAttribute('data-old-time');
                        hours.forEach(timeStr => {
                            const isSelected = (oldTime === timeStr) ? 'selected' : '';
                            timeSelect.innerHTML += `<option value="${timeStr}" ${isSelected}>${timeStr}</option>`;
                        });
                        // Clear old-time after first use
                        timeSelect.removeAttribute('data-old-time');
                    }
                })
                .catch(err => {
                    console.error("Error cargando horas:", err);
                    timeSelect.innerHTML = '<option value="">Error al cargar horas</option>';
                });
        }

        dateInput.addEventListener('change', updateAvailableHours);
        selectServicio.addEventListener('change', updateAvailableHours);
        
        // Si hay una fecha (ej. por old_input), cargar horas iniciales
        if (dateInput.value && selectServicio.value) {
            updateAvailableHours();
        }
    }

    if (formReserva) {
        formReserva.addEventListener('submit', function(e) {
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
});
</script>
