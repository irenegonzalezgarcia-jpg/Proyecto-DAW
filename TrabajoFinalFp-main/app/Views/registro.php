<section class="section1">
    <div>
        <h1>DATOS PERSONALES</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="color: red; margin-bottom: 15px; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/registro" method="POST" class="form-contacto" id="formularioRegistro" novalidate>
            <input type="text" id="nombreUsuario" name="nombre" placeholder="NOMBRE*" required>
            <input type="text" id="apellidosUsuario" name="apellidos" placeholder="APELLIDOS*" required>
            <input type="email" id="emailUsuario" name="email" placeholder="EMAIL*" required>
            <input type="password" id="contrasenaUsuario" name="password" placeholder="CONTRASEÑA*" required>
            <input type="password" id="confirmarContrasenaUsuario" name="confirm_password" placeholder="CONFIRMAR CONTRASEÑA*" required>
            <input type="text" id="telefonoUsuario" name="telefono" placeholder="TELÉFONO">

            <button type="submit" class="btn-cuenta">CREAR CUENTA</button>
        </form>
    </div>
</section>
