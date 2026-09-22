<section class="section" style="padding: 40px; background-color: #fcfcfc;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Gestión de Tratamientos</h2>
        <a href="/admin" style="background-color: var(--color-dark); color: white; padding: 10px 20px; border-radius: 4px; font-weight: bold;">Volver al Panel</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: var(--accent-pastel); text-align: left;">
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">ID</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Nombre</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Categoría</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Precio</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Promoción</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Duración (min)</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Estado</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($treatments as $t): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;"><?= $t['id'] ?></td>
                        <td style="padding: 15px; font-weight: bold;"><?= htmlspecialchars($t['name']) ?></td>
                        <td style="padding: 15px;"><?= htmlspecialchars($t['category']) ?></td>
                        <td style="padding: 15px;">
                            <form action="/admin/tratamientos/price" method="POST" style="margin: 0; display: flex; align-items: center; gap: 5px;">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <input type="number" step="0.01" name="price" value="<?= $t['price'] ?>" style="width: 70px; padding: 4px; text-align: right; border: 1px solid #ccc; border-radius: 4px;"> €
                                <button type="submit" style="background-color: var(--color-dark); color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.8em; font-weight: bold;">Guardar</button>
                            </form>
                        </td>
                        <td style="padding: 15px;">
                            <form action="/admin/tratamientos/promo" method="POST" style="margin: 0; display: flex; align-items: center; gap: 5px;">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <div style="display:flex; flex-direction:column; gap: 5px;">
                                    <div style="display:flex; align-items:center; gap: 5px;">
                                        <input type="number" step="0.01" max="<?= $t['price'] - 0.01 ?>" name="promo_price" value="<?= $t['promo_price'] ?>" placeholder="Precio" style="width: 70px; padding: 4px; text-align: right; border: 1px solid #ccc; border-radius: 4px;"> €
                                    </div>
                                    <div style="display:flex; align-items:center; gap: 5px; font-size:0.9em;">
                                        <input type="checkbox" name="is_promo" value="1" <?= $t['is_promo'] ? 'checked' : '' ?>> Activa
                                    </div>
                                </div>
                                <button type="submit" style="background-color: #c4a47c; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.8em; font-weight: bold;">Guardar</button>
                            </form>
                        </td>
                        <td style="padding: 15px;"><?= $t['duration_minutes'] ?> + <?= $t['cleaning_time_minutes'] ?> limp.</td>
                        <td style="padding: 15px;">
                            <?php if ($t['is_active']): ?>
                                <span style="background-color: #d0d8cd; color: #1f3822; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">Disponible</span>
                            <?php else: ?>
                                <span style="background-color: #f2d6d6; color: #6b2121; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: bold;">No Disponible</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px;">
                            <form action="/admin/tratamientos/toggle" method="POST" style="margin: 0;">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <input type="hidden" name="status" value="<?= $t['is_active'] ? '0' : '1' ?>">
                                <button type="submit" style="background-color: <?= $t['is_active'] ? '#f8d7da' : '#d4edda' ?>; color: <?= $t['is_active'] ? '#721c24' : '#155724' ?>; border: 1px solid <?= $t['is_active'] ? '#f5c6cb' : '#c3e6cb' ?>; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                    <?= $t['is_active'] ? 'Desactivar' : 'Activar' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($treatments)): ?>
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center;">No hay tratamientos en la base de datos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
