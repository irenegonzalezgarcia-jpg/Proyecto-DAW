<section class="section">
    <h2>Nuestras Promociones</h2>

    <div class="promociones">
        <div class="row">
            <?php if (empty($treatments)): ?>
                <p style="text-align:center; width:100%; font-size: 1.2em; color: #555; padding: 40px 20px;">
                    En este momento no tenemos promociones activas.<br>
                    ¡Vuelve pronto para descubrir nuestras ofertas especiales!
                </p>
            <?php else: ?>
                <?php foreach ($treatments as $t): ?>
                    <?php 
                        $img = $t['image_url'] ?? '/imagenes/logo.png';
                    ?>
                    <div class="col-6 promo-item">
                        <div class="promocion">
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($t['name']) ?>" class="promo-img">
                            <div class="promo-texto">
                                <h3><?= htmlspecialchars($t['name']) ?></h3>
                                <p><?= htmlspecialchars($t['description'] ?? 'Tratamiento en promoción.') ?></p>
                                <div class="precio-promo" style="margin-bottom: 10px;">
                                    <span class="antes" style="text-decoration: line-through; color: #999; margin-right: 10px; font-size: 1.1em;"><?= number_format($t['price'], 2) ?>€</span>
                                    <span class="ahora" style="color: #c4a47c; font-weight: bold; font-size: 1.4em;"><?= number_format($t['promo_price'], 2) ?>€</span>
                                </div>
                                <form action="/cesta/add" method="POST" style="margin-top:10px;">
                                    <input type="hidden" name="treatment_id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="btn-inicio efecto" style="background-color:#c4a47c; color:#fff; border:none; padding:8px 15px; cursor:pointer; font-weight:bold;">Añadir a la cesta</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
