<section class="section1">
    <div>
        <h1>RECUPERAR CONTRASEÑA</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="color: red; margin-bottom: 15px; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/recuperar-password" method="POST" class="form-contacto" novalidate>
            <p style="margin-bottom: 20px; text-align: center;">Introduce tu correo electrónico asociado a la cuenta para establecer una nueva contraseña.</p>
            <input type="email" name="email" placeholder="EMAIL*" required>
            <button type="submit" class="btn-cuenta">CONTINUAR</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="/login" style="color: var(--text-muted); font-size: 0.9em; text-decoration: underline;">Volver a iniciar sesión</a>
            </div>
        </form>
    </div>
</section>
