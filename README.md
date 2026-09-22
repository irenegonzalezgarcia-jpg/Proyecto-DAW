# 💅 Inspire Beauty - Plataforma de Gestión de Citas y E-commerce

> Proyecto técnico desarrollado como Trabajo Fin de Grado (TFG) para el ciclo formativo de Grado Superior en Desarrollo de Aplicaciones Web (DAW).

---

## 📖 Descripción del Proyecto

**Inspire Beauty** es una aplicación web integral concebida para modernizar y digitalizar la operativa de un centro de estética y bienestar. El sistema sustituye las tradicionales agendas físicas y reservas telefónicas por un ecosistema digital dual que cubre tanto el flujo de navegación, compra y reserva del cliente final como la gestión integral del negocio para el personal administrativo (CRM, agenda y control financiero).

El proyecto destaca por su arquitectura **Vanilla**, prescindiendo deliberadamente de frameworks de terceros (como Bootstrap o Laravel) para afianzar los fundamentos de la ingeniería web, garantizar un rendimiento de carga óptimo y lograr un diseño completamente adaptativo (*Mobile First*).

---

## 👥 Autores y Roles

* **Irene González García**: Responsable de la Capa de Presentación (Frontend, Maquetación CSS pura, UX/UI, maquetado semántico, accesibilidad y lógica cliente).
* **Inmaculada Hortelano**: Responsable de la Lógica de Servidor (Backend en PHP 8 bajo arquitectura MVC, persistencia en SQLite, seguridad, enrutador frontal y microservicio simulador de correo).

---

## 🛠️ Tecnologías y Arquitectura

### Frontend (Desarrollado por Irene)
* **HTML5 Semántico**: Marcado accesible (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`) auditado bajo estándares W3C Nu HTML Checker.
* **CSS3 Avanzado (Vanilla CSS)**: 
  * Arquitectura con Variables Nativas (`:root`) para la paleta de identidad corporativa.
  * Maquetación responsiva personalizada mediante rejilla flexible de 12 columnas con **Flexbox** y Media Queries bajo filosofía *Mobile First*.
  * Tipografías optimizadas vía Google Fonts (*Playfair Display* para encabezados y *Lato* para lectura).
  * Interactividad nativa mediante selectores avanzados y pseudo-clases (`:hover`, transiciones suaves, submenú desplegable sin librerías externas).
* **JavaScript Nativo (ES6+)**:
  * Formateo dinámico y asistido en tiempo real en formularios de pago (espaciado de tarjetas, fechas de caducidad).
  * Comunicación asíncrona mediante **Fetch API / AJAX** para la consulta en tiempo real de huecos de agenda sin recargar la página.

### Backend (Desarrollado por Inmaculada)
* **PHP 8 (Vanilla)** estructurado bajo el patrón **Modelo-Vista-Controlador (MVC)**.
* **Front Controller & Custom Router**: Enrutador a medida con soporte para URLs amigables (`/reservar`, `/cesta`, etc.).
* **Base de Datos SQLite**: Persistencia relacional serverless normalizada (`users`, `treatments`, `appointments`, `sales`, etc.).
* **Seguridad y Acceso a Datos**:
  * Consultas preparadas mediante **PDO** contra inyecciones SQL (SQLi).
  * Encriptado asimétrico de contraseñas con **Bcrypt** (`password_hash` y `password_verify`).
  * Sanitización con `htmlspecialchars` contra ataques XSS.
  * Control de sesiones con `$_SESSION` y protección de rutas por roles (`client` vs `admin`).
* **Simulador de Correo Electrónico (`EmailService`)**: Motor autocontenido para la generación de tickets de compra y avisos de citas en HTML mediante almacenamiento local.

### Herramientas de Entorno y Planificación
* **IDE**: Visual Studio Code (extensiones: Live Server, Prettier, PHP Intelephense, SQLite Viewer, CSS Peek).
* **Control de Versiones**: Git & GitHub con flujo estructurado de ramas (*branching*) y *merges*.
* **Metodología**: Metodologías Ágiles con tablero Kanban en **Trello**.

---

## 🚀 Funcionalidades Principales

### 👤 Área Pública y Perfil Cliente
* **Escaparate Digital (Home)**: Banner principal, servicios destacados y promociones vigentes.
* **Catálogo Detallado**: Vistas informativas de tratamientos faciales y corporales.
* **Tienda y Cesta de la Compra**: Añadido de tratamientos, desglose de importes y cabecera con contador dinámico en tiempo real.
* **Pasarela de Pago Simulada (Checkout)**: Formulario interactivo con asistente de tarjeta, pantalla de simulación de cobro bancario y generación de resguardo/ticket digital.
* **Motor de Reservas Online**: Selección de fecha mediante datepicker nativo y cálculo algorítmico de franjas horarias disponibles según la duración del tratamiento y márgenes de limpieza.
* **Panel del Cliente (Mi Panel)**: Histórico de tratamientos adquiridos y estado de citas (Pendiente, Confirmada, Cancelada) con opción de cancelación autónoma.

### 💼 Back-Office y Modo Administración (`/admin`)
* **Dashboard Financiero y Operativo**: KPIs en tiempo real (clientes registrados, ingresos totales, ventas y citas pendientes).
* **Calendario Interactivo**: Visualización mensual con código semafórico de colores por estado de cita y filtro cronológico diario.
* **Gestión Transaccional de Citas**: Aprobación, cancelación o reprogramación (*Reschedule*) de citas.
* **Gestor de Tratamientos (CRUD)**: Control de precios (PVP), activación/desactivación de visibilidad en web y gestión de ofertas promocionales.
* **Modo Mostrador / CRM**: Alta inmediata de clientes presenciales con creación de *Shadow Accounts* latentes y agendado manual con validación AJAX contra solapamientos.

---

## 📁 Estructura del Proyecto

* **`app/`**: Controladores del sistema (Auth, Appointment, Cart, Admin), Modelos SQL (User, Treatment, Appointment...) y Vistas dinámicas en PHP.
* **`core/`**: Componentes base de la arquitectura (Database Singleton, EmailService, Router).
* **`database/`**: Archivo de base de datos SQLite (.sqlite) y esquemas iniciales.
* **`frontend/`**: Hojas de estilo modulares (`css/` con variables `:root`), scripts de cliente (`js/` con AJAX y validaciones) y recursos multimedia (`imagenes/`).
* **`public/`**: Punto de entrada Front Controller (`index.php`) y assets estáticos.
* **`storage/`**: Historial de tickets y correos generados por EmailService.
* **`Wireframes/`**: Bocetos iniciales de diseño y UX del proyecto.

---

## ⚙️ Cómo ejecutar y abrir la aplicación web en tu ordenador

> 💡 **Nota importante:** Este proyecto está desarrollado en PHP 8 con arquitectura MVC y base de datos SQLite, por lo que no se ejecuta abriendo directamente los archivos `.html` o `.php` en el navegador. Requiere levantar un entorno de servidor web local.

### Opción 1: La forma más rápida (Servidor interno de PHP)

*Recomendado: no necesitas XAMPP ni herramientas externas, solo tener PHP instalado en tu equipo y accesible en el sistema.*

1. **Abrir la terminal en la raíz del proyecto:**
   * Entra a la carpeta del proyecto en tu explorador de archivos.
   * En la barra de direcciones de la carpeta, escribe `cmd` y presiona `Enter` (o abre la carpeta en VS Code y usa el atajo `Ctrl + Ñ`).
2. **Levantar el servidor web:**
   Ejecuta el siguiente comando apuntando al punto de entrada público (`public`):
   ```bash
   php -S localhost:8000 -t public
   ```
3. **Acceder a la aplicación:**
   * Abre tu navegador web y entra en: [http://localhost:8000](http://localhost:8000).
4. **Detener el servidor:**
   * Pulsa `Ctrl + C` en la terminal para apagar el servicio.

---

### Opción 2: Despliegue tradicional (XAMPP / Laragon).

Si prefieres trabajar con una suite de servidores locales:

1. **Ubicación de archivos:**
   * Mueve o clona la carpeta del proyecto dentro del directorio raíz del servidor:
     * En XAMPP: `C:\xampp\htdocs\TrabajoFinalFp`
     * En Laragon: `C:\laragon\www\TrabajoFinalFp`
2. **Iniciar servicios:**
   * Abre el panel de control de tu servidor e inicia exclusivamente el servicio **Apache**.
   * *(Nota: El servicio MySQL debe permanecer apagado, ya que la persistencia se realiza íntegramente sobre el archivo SQLite local y no requiere un gestor SQL externo)*.
3. **Acceder a la aplicación:**
   * Abre tu navegador e ingresa a: `http://localhost/TrabajoFinalFp/public`.

---

## 🔑 Credenciales de Acceso para Pruebas

Para agilizar la evaluación de los distintos roles y flujos de la plataforma, se encuentran disponibles las siguientes cuentas de prueba precargadas:

| Rol | Correo Electrónico | Contraseña | Vistas y Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador (Staff)** | `admin@inspirebeauty.es` | `admin123` | Acceso a `/admin` (Back-Office, métricas financieras, CRM, gestión de agenda y catálogo. |
| **Cliente Estándar** | `prueba@prueba.com` | `cliente123` | Acceso a `/mi-panel` (historial transaccional, solicitud y cancelación autónoma de citas). |
