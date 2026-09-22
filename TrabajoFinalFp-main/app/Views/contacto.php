<!--1ºSección-->
<section class="section1">
    <div>
        <h3>CONTACTO</h3>
        <h1>Estamos a un paso de ti. Ven a visitarnos</h1>
    </div>
</section>

<!--2º Sección-->
<section class="section2">
    <div class="row">
        <div class="primero col-6">
            <h4>CONTACTA CON NUESTRO CENTRO:</h4>
            <div class="direccion">
                <p>Direccion</p>
                <p>Calle Nueva 2</p>
                <p>28921 Alcorcón</p>
            </div>

            <div class="separar">
                <p>Teléfonos:</p>
                <p>916 43 83 26</p>
                <p>685 37 98 62</p>
            </div>

            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3040.756606611871!2d-3.8275822!3d40.347744999999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd418eca7f406589%3A0x7199fb37c5505389!2sCentro%20de%20Formaci%C3%B3n%20Profesional%20Juan%20XXIII!5e0!3m2!1ses!2ses!4v1775149749475!5m2!1ses!2ses"
                width="400" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="segundo col-6">
            <h4>O ESCRÍBENOS:</h4>
            
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

            <form class="form-contacto" action="/contacto" method="POST" novalidate>
                <input type="text" id="nombreUsuario" name="nombre" placeholder="Nombre y apellidos*" required>
                <input type="email" id="emailUsuario" name="email" placeholder="Email*" required>
                <input type="tel" id="telefonoUsuario" name="telefono" placeholder="Teléfono*" required>
                <textarea id="mensajeUsuario" name="mensaje" placeholder="Cuéntanos..." required></textarea>

                <button type="submit" class="btn-enviar">Enviar</button>
            </form>
            <p>*Campos obligatorios</p>
            <img src="/imagenes/pexels-zandatsu-16571736.jpg" alt="">
        </div>

    </div>

</section>
