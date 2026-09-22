<section class="section1" style="padding: 100px 20px; text-align: center; background-color: #fcfcfc; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    
    <div id="processing-container">
        <!-- Spinner CSS simple -->
        <style>
            .spinner {
                border: 8px solid #f3f3f3; /* Light grey */
                border-top: 8px solid var(--hover-nav); /* Dark grey */
                border-radius: 50%;
                width: 60px;
                height: 60px;
                animation: spin 2s linear infinite;
                margin: 0 auto 30px auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>

        <div class="spinner"></div>
        <h2>Procesando su pago...</h2>
        <p style="color: var(--text-muted); margin-top: 15px;">Por favor, no cierre ni actualice esta ventana.</p>
        <p style="color: var(--text-muted); font-size: 0.9em; margin-top: 5px;">Conectando de forma segura con su banco.</p>
    </div>

    <script>
        // Redirigir a la página de éxito después de 3 segundos
        setTimeout(function() {
            window.location.href = '/checkout/success';
        }, 3000);
    </script>
</section>
