<section class="section1">
    <div>
        <h1>REESTABLECER CONTRASEÑA</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="color: red; margin-bottom: 15px; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/restablecer-password" method="POST" class="form-contacto" novalidate>
            <input type="email" value="<?= htmlspecialchars($_SESSION['reset_email']) ?>" readonly style="background-color: #f9f9f9; color: #888;">
            
            <input type="password" name="password" placeholder="NUEVA CONTRASEÑA*" required>
            <input type="password" name="confirm_password" placeholder="CONFIRMAR CONTRASEÑA*" required>

            <button type="submit" class="btn-cuenta">REESTABLECER CONTRASEÑA</button>
        </form>
    </div>
</section>
