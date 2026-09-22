<section class="section">
    <h2>Tratamientos</h2>

    <div class="promociones">
        <div class="row">
            <?php if (empty($treatments)): ?>
                <p style="text-align:center; width:100%;">No hay tratamientos disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($treatments as $t): ?>
                    <?php 
                        $img = $t['image_url'] ?? '/imagenes/logo.png';
                        $opacity = $t['is_active'] ? '1' : '0.5';
                        $filter = $t['is_active'] ? 'none' : 'grayscale(100%)';
                    ?>
                    <div class="col-6 promo-item">
                        <div class="promocion" style="opacity: <?= $opacity ?>; filter: <?= $filter ?>; position:relative;">
                            <?php if (!$t['is_active']): ?>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); color: white; padding: 10px 20px; font-weight: bold; border-radius: 4px; z-index: 10;">
                                    No disponible temporalmente
                                </div>
                            <?php endif; ?>
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($t['name']) ?>" class="promo-img">
                            <div class="promo-texto">
                                <h3><?= htmlspecialchars($t['name']) ?></h3>
                                <p><?= htmlspecialchars($t['description'] ?? 'Tratamiento ' . strtolower($t['category']) . ' en Inspire Beauty.') ?></p>
                                <div class="precio-promo" style="margin-bottom: 10px;">
                                    <?php if ($t['is_promo'] && $t['promo_price']): ?>
                                        <span class="antes" style="text-decoration: line-through; color: #999; margin-right: 10px; font-size: 1.1em;"><?= number_format($t['price'], 2) ?>€</span>
                                        <span class="ahora" style="color: #c4a47c; font-weight: bold; font-size: 1.4em;"><?= number_format($t['promo_price'], 2) ?>€</span>
                                    <?php else: ?>
                                        <span class="ahora" style="font-weight: bold; font-size: 1.2em;"><?= number_format($t['price'], 2) ?>€</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($t['is_active']): ?>
                                    <form action="/cesta/add" method="POST" style="margin-top:10px;">
                                        <input type="hidden" name="treatment_id" value="<?= $t['id'] ?>">
                                        <button type="submit" class="btn-inicio efecto" style="background-color:#c4a47c; color:#fff; border:none; padding:8px 15px; cursor:pointer; font-weight:bold;">Añadir a la cesta</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
