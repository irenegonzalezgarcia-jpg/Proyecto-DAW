<section class="section1" style="padding: 40px; text-align: left; background-color: #fcfcfc;">
    <h2 style="text-align: center; margin-bottom: 30px;">Finalizar Compra</h2>
    
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="margin-bottom: 20px; text-align: center;">
            <h3>Total a Pagar: <span style="color: #4a4a4a; font-size: 1.5em;"><?= number_format($total, 2) ?>€</span></h3>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <form action="/checkout/process" method="POST" class="form-contacto" id="formularioCheckout">
                <h4 style="margin-bottom: 15px; color: var(--text-muted); border-bottom: 1px solid #eee; padding-bottom: 5px;">Venta en Tienda (Mostrador)</h4>
                
                <input type="text" name="client_name" placeholder="NOMBRE DEL CLIENTE*" required style="width: 100%; margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                <input type="email" name="client_email" placeholder="CORREO ELECTRÓNICO DEL CLIENTE*" required style="width: 100%; margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                
                <h4 style="margin-top: 20px; margin-bottom: 15px; color: var(--text-muted); border-bottom: 1px solid #eee; padding-bottom: 5px;">Método de Pago</h4>
                
                <select name="payment_method" required style="width: 100%; margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-family: 'Lato', sans-serif;">
                    <option value="" disabled selected>Seleccione un método de pago...</option>
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta (Datáfono)">Tarjeta (Datáfono)</option>
                </select>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn-inicio" style="border: 2px solid var(--color-dark); font-size: 1.1em; padding: 15px 30px; width: 100%;">Registrar Venta de <?= number_format($total, 2) ?>€</button>
                    <a href="/cesta" style="display: block; margin-top: 15px; color: var(--text-muted); text-decoration: underline;">Cancelar y volver a la cesta</a>
                </div>
            </form>
        <?php else: ?>
            <form action="/checkout/process" method="POST" class="form-contacto" id="formularioCheckout">
                <h4 style="margin-bottom: 15px; color: var(--text-muted); border-bottom: 1px solid #eee; padding-bottom: 5px;">Datos de Facturación</h4>
                <input type="text" name="billing_address" placeholder="DIRECCIÓN COMPLETA*" required style="width: 100%; margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                <input type="text" name="billing_city" placeholder="CIUDAD*" required style="width: 100%; margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                
                <h4 style="margin-top: 20px; margin-bottom: 15px; color: var(--text-muted); border-bottom: 1px solid #eee; padding-bottom: 5px;">Datos de Pago</h4>
                <input type="text" name="card_name" placeholder="TITULAR DE LA TARJETA*" required style="width: 100%; margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                
                <!-- Simulador de tarjeta -->
                <input type="text" name="card_number" id="numero_tarjeta" placeholder="NÚMERO DE TARJETA (16 dígitos)*" maxlength="19" required style="width: 100%; margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace;">
                
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <input type="text" name="card_expiry" id="caducidad_tarjeta" placeholder="MM/AA*" maxlength="5" required style="flex: 1; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                    <input type="text" name="card_cvv" id="cvv_tarjeta" placeholder="CVV*" maxlength="3" required style="flex: 1; padding: 12px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn-inicio" style="border: 2px solid var(--color-dark); font-size: 1.1em; padding: 15px 30px; width: 100%;">Procesar Pago de <?= number_format($total, 2) ?>€</button>
                    <a href="/cesta" style="display: block; margin-top: 15px; color: var(--text-muted); text-decoration: underline;">Cancelar y volver a la cesta</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const numeroTarjeta = document.getElementById('numero_tarjeta');
    const caducidadTarjeta = document.getElementById('caducidad_tarjeta');
    const cvvTarjeta = document.getElementById('cvv_tarjeta');

    if (numeroTarjeta) {
        numeroTarjeta.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Eliminar letras
            // Agrupar de 4 en 4
            value = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = value;
        });
    }

    if (caducidadTarjeta) {
        caducidadTarjeta.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Eliminar letras
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }

    if (cvvTarjeta) {
        cvvTarjeta.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 3);
        });
    }
});
</script>
</section>
