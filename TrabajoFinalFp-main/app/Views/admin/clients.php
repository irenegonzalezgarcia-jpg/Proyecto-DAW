<section class="section1 admin-dashboard">
    <h2>Gestión de Clientes</h2>
    <div class="admin-actions" style="margin-bottom: 20px; text-align: left;">
        <a href="/admin" class="btn-inicio" style="margin-right: 10px;">Volver al Dashboard</a>
    </div>

    <div style="background-color: #f2eadf; padding: 20px; border-radius: 8px; overflow-x: auto;">
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #5a4a42; color: #5a4a42;">
                    <th style="padding: 10px;">Nombre y Apellidos</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Estado de Cuenta</th>
                    <th style="padding: 10px;">Tratamientos Comprados</th>
                    <th style="padding: 10px;">Total Gastado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 10px;">No hay clientes registrados en el sistema.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($clients as $client): ?>
                        <?php 
                            $esMostrador = ($client['password_hash'] === 'CUENTA_MOSTRADOR');
                        ?>
                        <tr style="border-bottom: 1px solid #ccc; <?= $esMostrador ? 'background-color: rgba(255, 255, 255, 0.4);' : '' ?>">
                            <td style="padding: 10px;">
                                <strong><?= htmlspecialchars($client['name']) ?></strong>
                            </td>
                            <td style="padding: 10px;"><?= htmlspecialchars($client['email']) ?></td>
                            <td style="padding: 10px;">
                                <?php if ($esMostrador): ?>
                                    <span style="background-color: #ffc107; color: #000; padding: 3px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">Invitado (Mostrador)</span>
                                <?php else: ?>
                                    <span style="background-color: #28a745; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">Activa (Web)</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px;">
                                <?php if (!empty($client['products'])): ?>
                                    <ul style="margin: 0; padding-left: 15px; font-size: 0.9em; color: var(--text-muted); font-style: italic;">
                                        <?php foreach ($client['products'] as $product): ?>
                                            <li><?= htmlspecialchars($product) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span style="color: #999; font-style: italic;">Sin compras</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 10px;">
                                <strong><?= number_format($client['total_spent'] ?? 0, 2) ?>€</strong>
                                <br>
                                <small style="color: #666;">(<?= $client['total_orders'] ?> pedidos)</small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
