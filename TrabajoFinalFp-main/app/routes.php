<?php
// Archivo de rutas de la aplicación

// Páginas estáticas (Frontend actual)
$router->get('/', 'HomeController@index');
$router->get('/facial', 'HomeController@facial');
$router->get('/corporal', 'HomeController@corporal');
$router->get('/tratamientos', 'HomeController@tratamientos');
$router->get('/promociones', 'HomeController@promociones');
$router->get('/contacto', 'HomeController@contacto');
$router->post('/contacto', 'HomeController@submitContact');
$router->get('/ver-correo', 'HomeController@viewEmail');

// Autenticación
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/registro', 'AuthController@register');
$router->post('/registro', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/completar-registro', 'AuthController@completeRegister');
$router->post('/completar-registro', 'AuthController@completeRegister');
$router->get('/recuperar-password', 'AuthController@forgotPassword');
$router->post('/recuperar-password', 'AuthController@forgotPassword');
$router->get('/restablecer-password', 'AuthController@resetPassword');
$router->post('/restablecer-password', 'AuthController@resetPassword');

// Paneles
$router->get('/panel', 'ClientController@dashboard');
$router->post('/client/appointments/confirm', 'ClientController@confirmReschedule');
$router->post('/client/appointments/cancel', 'ClientController@cancelAppointment');
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/reservar', 'AdminController@calendar');
$router->get('/admin/clientes', 'AdminController@clients');
$router->get('/admin/tratamientos', 'AdminController@treatments');
$router->post('/admin/tratamientos/toggle', 'AdminController@toggleTreatment');
$router->post('/admin/tratamientos/price', 'AdminController@updateTreatmentPrice');
$router->post('/admin/tratamientos/promo', 'AdminController@updateTreatmentPromo');
$router->post('/admin/appointments/add', 'AdminController@addAppointment');
$router->post('/admin/appointments/status', 'AdminController@updateStatus');
$router->post('/admin/appointments/reschedule', 'AdminController@reschedule');

// Reservas (Público/Cliente)
$router->get('/reservar', 'AppointmentController@book');
$router->post('/reservar', 'AppointmentController@store');
$router->get('/api/available-hours', 'AppointmentController@getAvailableHours');

// Cesta
$router->get('/cesta', 'CartController@index');
$router->post('/cesta/add', 'CartController@add');
$router->post('/cesta/remove', 'CartController@remove');
$router->get('/checkout', 'CartController@checkout');
$router->post('/checkout/process', 'CartController@processPayment');
$router->get('/checkout/processing', 'CartController@processingScreen');
$router->get('/checkout/success', 'CartController@successScreen');
