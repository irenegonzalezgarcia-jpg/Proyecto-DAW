<section class="section1" style="padding: 40px; text-align: left;">
    <h2>Hola, <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div style="color: green; margin-bottom: 15px; font-weight: bold;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-6">
            <h3 style="margin-top: 20px;">Mis Próximas Citas</h3>
            <div style="background-color: var(--accent-pastel); padding: 20px; border-radius: 8px;">
                <?php if (empty($appointments)): ?>
                    <p>No tienes citas programadas.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($appointments as $apt): ?>
                            <?php 
                                $dateObj = new DateTime($apt['appointment_date']);
                                $formattedDate = $dateObj->format('d/m/Y');
                            ?>
                            <li style="position: relative; border-bottom: 1px solid #ccc; padding: 10px 0; <?= $apt['original_start_time'] && $apt['status'] === 'pending' ? 'background-color: #fff3cd; border-left: 4px solid #856404; padding-left: 10px;' : '' ?>">
                                <strong><?= htmlspecialchars($apt['treatment_name']) ?></strong><br>
                                
                                <?php if ($apt['original_start_time'] && $apt['status'] === 'pending'): ?>
                                    <div style="color: #856404; font-weight: bold; font-size: 0.9em; margin: 5px 0;">⚠️ Su cita ha sido modificada por el centro por motivos de agenda/limpieza.</div>
                                    <?php 
                                        $origDateObj = new DateTime($apt['original_appointment_date']);
                                        $origFormattedDate = $origDateObj->format('d/m/Y');
                                    ?>
                                    Fecha original: <del style="color: #888;"><?= $origFormattedDate ?> a las <?= htmlspecialchars($apt['original_start_time']) ?></del><br>
                                    <strong style="color: #2e7d32;">Nueva fecha: <?= $formattedDate ?> a las <?= htmlspecialchars($apt['start_time']) ?></strong>
                                    
                                    <div style="margin-top: 10px;">
                                        <form action="/client/appointments/confirm" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $apt['id'] ?>">
                                            <button type="submit" style="background-color: #2e7d32; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.9em;">✔ Confirmar modificación</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    Fecha: <?= $formattedDate ?> a las <?= htmlspecialchars($apt['start_time']) ?><br>
                                <?php endif; ?>
                                
                                <?php
                                $coloresEstado = [
                                    'pending' => ['bg' => '#dbe4ed', 'text' => '#2c3e50', 'label' => 'Pendiente'],
                                    'confirmed' => ['bg' => '#d0d8cd', 'text' => '#1f3822', 'label' => 'Confirmada'],
                                    'cancelled' => ['bg' => '#f2d6d6', 'text' => '#6b2121', 'label' => 'Cancelada']
                                ];
                                $st = $apt['status'];
                                $infoEstado = $coloresEstado[$st] ?? ['bg' => '#ddd', 'text' => '#000', 'label' => ucfirst($st)];
                                ?>
                                <div style="margin-top: 5px;">
                                    Estado: <span style="padding: 2px 8px; border-radius: 4px; background-color: <?= $infoEstado['bg'] ?>; font-size: 0.85em; color: <?= $infoEstado['text'] ?>; font-weight: bold;">
                                        <?= htmlspecialchars($infoEstado['label']) ?>
                                    </span>
                                </div>
                                
                                <?php
                                $appointmentDateTime = new DateTime($apt['appointment_date'] . ' ' . $apt['start_time']);
                                $now = new DateTime();
                                $diffHours = ($appointmentDateTime->getTimestamp() - $now->getTimestamp()) / 3600;
                                $canCancel = ($diffHours >= 24) && in_array($apt['status'], ['pending', 'confirmed']);
                                
                                if ($canCancel): ?>
                                    <div style="position: absolute; top: 10px; right: 0;">
                                        <form action="/client/appointments/cancel" method="POST" style="margin: 0;" onsubmit="event.preventDefault(); Swal.fire({ title: '¿Cancelar cita?', text: '¿Estás seguro de que deseas cancelar esta cita?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d9534f', cancelButtonColor: '#aaa', confirmButtonText: 'Sí, cancelar', cancelButtonText: 'No, mantener' }).then((result) => { if (result.isConfirmed) { this.submit(); } });">
                                            <input type="hidden" name="id" value="<?= $apt['id'] ?>">
                                            <button type="submit" style="background-color: #d9534f; color: white; border: none; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.8em;">❌ Cancelar cita</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <br>
                <a href="/reservar" class="btn-inicio">Reservar nueva cita</a>
            </div>
        </div>
        <div class="col-6">
            <h3 style="margin-top: 20px;">Mi Historial de Compras</h3>
            <div style="background-color: var(--bg-promo-green-light); padding: 20px; border-radius: 8px;">
                <?php if (empty($purchases)): ?>
                    <p>No has realizado ninguna compra.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($purchases as $p): ?>
                            <?php 
                                $pDateObj = new DateTime($p['purchase_date']);
                                $pFormattedDate = $pDateObj->format('d/m/Y H:i');
                            ?>
                            <li style="border-bottom: 1px solid #ccc; padding: 10px 0;">
                                Compra #<?= htmlspecialchars($p['id']) ?> - <?= $pFormattedDate ?><br>
                                <span style="font-style:italic; color:#555; display:block; margin: 4px 0;"><?= htmlspecialchars($p['products']) ?></span>
                                <strong>Total: <?= number_format($p['total_amount'], 2) ?>€</strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <br>
                <a href="/tratamientos" class="btn-inicio">Ver tratamientos</a>
            </div>
        </div>
    </div>
</section>
