<section class="section1">
    <div class="row">
        <div class="col-6">
            <h1>INSPIRE BEAUTY</h1>
            <p class="txt-exclusivo">
                Área privada de clientes y personal del centro.
            </p>
            <h3>INICIA SESIÓN O REGÍSTRATE</h3>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div style="color: red; margin-bottom: 15px; font-weight: bold;">
                    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div style="color: green; margin-bottom: 15px; font-weight: bold;">
                    <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="/login" method="POST" class="form-contacto" id="formularioLogin" novalidate>
                <input type="email" id="correoLogin" name="email" placeholder="EMAIL*" required>
                <input type="password" id="contrasenaLogin" name="password" placeholder="CONTRASEÑA*" required>

                <button type="submit" class="btn-sesion">INICIAR SESIÓN</button>
                <a href="/registro" class="btn-registro">REGÍSTRATE</a>
                
                <div style="text-align: center; margin-top: 15px;">
                    <a href="/recuperar-password" style="color: var(--text-muted); font-size: 0.9em; text-decoration: underline;">¿Has olvidado tu contraseña?</a>
                </div>
            </form>
        </div>
        <div class="col-6">
            <img src="/imagenes/pexels-burakeroglu3-35495081.jpg" alt="">
        </div>
    </div>
</section>
