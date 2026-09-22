<section class="section1" style="padding: 40px; text-align: left;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Panel de Administración</h2>
        <div style="display: flex; gap: 10px;">
            <a href="/admin/clientes" style="background-color: var(--color-dark); color: white; padding: 10px 20px; border-radius: 4px; font-weight: bold; text-decoration: none;">Gestión de Clientes</a>
            <a href="/admin/tratamientos" style="background-color: var(--color-dark); color: white; padding: 10px 20px; border-radius: 4px; font-weight: bold; text-decoration: none;">Gestión de Tratamientos</a>
        </div>
    </div>
    
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
        <!-- CITAS PENDIENTES GLOBALES -->
        <div class="col-12" style="margin-bottom: 40px;">
            <h3>Citas Pendientes de Confirmar</h3>
            <div style="background-color: #f2d6d6; padding: 20px; border-radius: 8px; overflow-x: auto; border-left: 4px solid #b71c1c;">
                <?php 
                $pendingApts = array_filter($appointments, function($a) { return $a['status'] === 'pending'; });
                if (empty($pendingApts)): 
                ?>
                    <p style="color: #6b2121; font-weight: bold; margin:0;">¡Todo al día! No hay citas pendientes de confirmar.</p>
                <?php else: ?>
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #6b2121; color: #6b2121;">
                                <th style="padding: 10px;">Fecha y Hora</th>
                                <th style="padding: 10px;">Cliente</th>
                                <th style="padding: 10px;">Tratamiento</th>
                                <th style="padding: 10px; text-align:right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingApts as $apt): ?>
                                <?php 
                                    $dateObj = new DateTime($apt['appointment_date']);
                                    $formattedDate = $dateObj->format('d/m/Y');
                                ?>
                                <tr style="border-bottom: 1px solid #d9adad;">
                                    <td style="padding: 10px; font-weight:bold;"><?= $formattedDate ?> (<?= htmlspecialchars($apt['start_time']) ?>)</td>
                                    <td style="padding: 10px;"><?= htmlspecialchars($apt['user_name']) ?></td>
                                    <td style="padding: 10px;"><?= htmlspecialchars($apt['treatment_name']) ?></td>
                                    <td style="padding: 10px;">
                                        <div style="display:flex; justify-content:flex-end; gap:5px;">
                                            <form action="/admin/appointments/status" method="POST" style="margin:0;">
                                                <input type="hidden" name="id" value="<?= $apt['id'] ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" style="background-color:#d0d8cd; border:1px solid #1f3822; color:#1f3822; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:0.85em; font-weight:bold;">✔ Confirmar</button>
                                            </form>
                                            <form action="/admin/appointments/status" method="POST" style="margin:0;">
                                                <input type="hidden" name="id" value="<?= $apt['id'] ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" style="background-color:white; border:1px solid #6b2121; color:#6b2121; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:0.85em; font-weight:bold;">✖ Cancelar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- HISTORIAL DE VENTAS -->
        <div class="col-12" style="margin-bottom: 40px;">
            <h3>Ventas Recientes</h3>
            <div style="background-color: #f4ecd8; padding: 20px; border-radius: 8px; overflow-x: auto;">
                <?php if (empty($purchases)): ?>
                    <p>No hay ventas registradas.</p>
                <?php else: ?>
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #333;">
                                <th style="padding: 10px;">ID Compra</th>
                                <th style="padding: 10px;">Fecha</th>
                                <th style="padding: 10px;">Modalidad</th>
                                <th style="padding: 10px;">Cliente</th>
                                <th style="padding: 10px;">Productos</th>
                                <th style="padding: 10px;">Pago</th>
                                <th style="padding: 10px; text-align:right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchases as $p): ?>
                                <?php 
                                    $pDateObj = new DateTime($p['purchase_date']);
                                    $pFormattedDate = $pDateObj->format('d/m/Y H:i');
                                ?>
                                <tr style="border-bottom: 1px solid #ccc;">
                                    <td style="padding: 10px;">#<?= htmlspecialchars($p['id']) ?></td>
                                    <td style="padding: 10px;"><?= $pFormattedDate ?></td>
                                    <td style="padding: 10px;">
                                        <?php if (strpos($p['card_name'] ?? '', 'Cliente: ') === 0): ?>
                                            <span style="background-color: #ffe0b2; padding: 3px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold; color: #e65100;">Mostrador</span>
                                        <?php else: ?>
                                            <span style="background-color: #c8e6c9; padding: 3px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold; color: #1b5e20;">Tienda Online</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 10px;">
                                        <?php if (strpos($p['card_name'] ?? '', 'Cliente: ') === 0): ?>
                                            <strong><?= htmlspecialchars(substr($p['card_name'], 9)) ?></strong><br>
                                            <small style="color: #555;"><?= htmlspecialchars($p['user_email']) ?></small>
                                        <?php else: ?>
                                            <strong><?= htmlspecialchars($p['user_name']) ?></strong><br>
                                            <small style="color: #555;"><?= htmlspecialchars($p['user_email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 10px; font-style:italic; color:#555;"><?= htmlspecialchars($p['products']) ?></td>
                                    <td style="padding: 10px;">
                                        <?php if ($p['card_last_four']): ?>
                                            <?php if ($p['card_last_four'] === 'Efectivo'): ?>
                                                <span style="font-size: 0.9em; background-color: #e0e0e0; padding: 2px 6px; border-radius: 4px;">💵 Efectivo</span>
                                            <?php elseif ($p['card_last_four'] === 'Datáfono'): ?>
                                                <span style="font-size: 0.9em; background-color: #e0e0e0; padding: 2px 6px; border-radius: 4px;">💳 Datáfono</span>
                                            <?php else: ?>
                                                <span style="font-size: 0.9em; background-color: #e0e0e0; padding: 2px 6px; border-radius: 4px;">💳 **** <?= htmlspecialchars($p['card_last_four']) ?></span>
                                            <?php endif; ?>
                                            <?php if (strpos($p['card_name'] ?? '', 'Cliente: ') !== 0): ?>
                                                <br><small style="color: #666;"><?= htmlspecialchars($p['card_name']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 0.9em;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 10px; text-align:right; font-weight:bold;"><?= number_format($p['total_amount'], 2) ?>€</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- BANDEJA DE MENSAJES -->
        <div class="col-12">
            <h3>Bandeja de Entrada de Contacto</h3>
            <div style="background-color: var(--bg-promo-green-light); padding: 20px; border-radius: 8px; overflow-x: auto;">
                <?php if (empty($messages)): ?>
                    <p>No hay mensajes nuevos.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($messages as $msg): ?>
                            <?php 
                                $mDateObj = new DateTime($msg['created_at']);
                                $mFormattedDate = $mDateObj->format('d/m/Y H:i');
                            ?>
                            <li style="border-bottom: 1px solid #ccc; padding: 15px 0;">
                                <strong><?= htmlspecialchars($msg['name']) ?></strong> (<?= htmlspecialchars($msg['email']) ?> - <?= htmlspecialchars($msg['phone']) ?>)<br>
                                <span style="font-size: 0.9em; color: gray;"><?= $mFormattedDate ?></span><br>
                                <p style="margin-top: 5px;"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
