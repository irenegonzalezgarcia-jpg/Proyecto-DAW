<section class="section1" style="padding: 60px 20px; text-align: center; background-color: #fcfcfc;">
    
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #d4edda; color: #155724; display: flex; justify-content: center; align-items: center; font-size: 40px; margin: 0 auto 20px auto;">
            ✓
        </div>
        
        <h2 style="color: #155724; margin-bottom: 15px;">¡Pago realizado con éxito!</h2>
        <p style="color: var(--text-main); font-size: 1.1em; margin-bottom: 30px;">Su pedido ha sido procesado correctamente y guardado en su panel.</p>
        
        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: #555; margin-bottom: 20px;"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        
        <?php if (isset($emailId)): ?>
            <div style="margin: 20px 0; padding: 15px; background-color: #f1f8ff; border: 1px solid #c8e1ff; border-radius: 5px;">
                <p style="margin-bottom: 10px; color: #0366d6;">Simulador de Correo Electrónico</p>
                <a href="/ver-correo?id=<?= $emailId ?>" target="_blank" style="display: inline-block; background-color: #0366d6; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                    📧 Ver Ticket del Cliente
                </a>
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
            <a href="/panel" class="btn-inicio btn-pastel">Ir a Mi Panel</a>
            <a href="/promociones" class="btn-inicio">Volver a la tienda</a>
        </div>
    </div>

</section>
