<section class="section1" style="padding: 40px; text-align: left;">
    <h2>Tu Cesta</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div style="color: green; margin-bottom: 15px; font-weight: bold;">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div style="color: red; margin-bottom: 15px; font-weight: bold;">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div style="background-color: var(--accent-pastel); padding: 20px; border-radius: 8px;">
        <?php if (empty($cartItems)): ?>
            <p>No tienes ningún tratamiento en la cesta.</p>
            <a href="/tratamientos" class="btn-inicio efecto">Ver tratamientos</a>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #333;">
                        <th style="padding: 10px; text-align: left;">Tratamiento</th>
                        <th style="padding: 10px; text-align: center;">Cantidad</th>
                        <th style="padding: 10px; text-align: right;">Precio</th>
                        <th style="padding: 10px; text-align: right;">Subtotal</th>
                        <th style="padding: 10px; text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($cartItems as $item): 
                        $img = $item['image_url'] ?? '/imagenes/logo.png';
                    ?>
                        <tr style="border-bottom: 1px solid #ccc;">
                            <td style="padding: 10px; display:flex; align-items:center; gap:15px;">
                                <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                <span style="font-weight:bold;"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td style="padding: 10px; text-align: center;"><?= htmlspecialchars($item['quantity']) ?></td>
                            <td style="padding: 10px; text-align: right;"><?= number_format($item['effective_price'], 2) ?>€</td>
                            <td style="padding: 10px; text-align: right; font-weight:bold;"><?= number_format($item['effective_price'] * $item['quantity'], 2) ?>€</td>
                            <td style="padding: 10px; text-align: center;">
                                <form action="/cesta/remove" method="POST" style="margin: 0;">
                                    <input type="hidden" name="treatment_id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <button type="submit" style="background: none; border: none; color: #d9534f; cursor: pointer; text-decoration: underline; font-weight:bold;">Quitar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="padding: 10px; text-align: right;">TOTAL:</th>
                        <th style="padding: 10px; text-align: right; font-size: 1.2em;"><?= number_format($total, 2) ?>€</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            
            <div style="text-align: right; margin-top: 20px; display: flex; justify-content: flex-end; align-items: center; gap: 20px;">
                <?php $linkSeguir = '/tratamientos'; ?>
                <a href="<?= $linkSeguir ?>" style="color: #333; text-decoration: underline; font-weight: bold;">Seguir comprando</a>
                <a href="/checkout" class="btn-inicio btn-beige" style="background-color: white; border: 2px solid var(--color-dark); padding: 10px 20px; font-weight: bold;">Pagar y Finalizar Compra</a>
            </div>
        <?php endif; ?>
    </div>
</section>
