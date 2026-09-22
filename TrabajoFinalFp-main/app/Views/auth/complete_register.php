<section class="section1">
    <div>
        <h1>COMPLETAR REGISTRO</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="color: red; margin-bottom: 15px; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/completar-registro" method="POST" class="form-contacto" id="formularioCompletarRegistro" novalidate>
            <input type="email" id="emailUsuario" value="<?= htmlspecialchars($_SESSION['pending_setup_email']) ?>" readonly style="background-color: #f9f9f9; color: #888;">
            
            <?php
                $parts = explode(' ', $_SESSION['pending_setup_name'] ?? '');
                $nombre = array_shift($parts);
                $apellidos = implode(' ', $parts);
            ?>
            <input type="text" id="nombreUsuario" name="nombre" placeholder="NOMBRE*" value="<?= htmlspecialchars($nombre) ?>" required>
            <input type="text" id="apellidosUsuario" name="apellidos" placeholder="APELLIDOS*" value="<?= htmlspecialchars($apellidos) ?>" required>
            
            <input type="password" id="contrasenaUsuario" name="password" placeholder="NUEVA CONTRASEÑA*" required>
            <input type="password" id="confirmarContrasenaUsuario" name="confirm_password" placeholder="CONFIRMAR CONTRASEÑA*" required>
            
            <input type="text" id="telefonoUsuario" name="telefono" placeholder="TELÉFONO">

            <button type="submit" class="btn-cuenta">COMPLETAR REGISTRO</button>
        </form>
    </div>
</section>
