<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Treatment;

class HomeController extends Controller {
    public function index() {
        $this->render('home', [
            'title' => 'Inspire Beauty - Centro de estética'
        ]);
    }

    public function facial() {
        $this->render('facial', [
            'title' => 'Tratamientos Faciales - Inspire Beauty',
            'extraCss' => ['/css/estilos1.css']
        ]);
    }

    public function corporal() {
        $this->render('corporal', [
            'title' => 'Tratamientos Corporales - Inspire Beauty',
            'extraCss' => ['/css/estilos1.css']
        ]);
    }

    public function promociones() {
        $allTreatments = Treatment::getAll(false);
        $promos = array_filter($allTreatments, function($t) {
            return $t['is_promo'] == 1;
        });

        $this->render('promociones', [
            'title' => 'Promociones - Inspire Beauty',
            'extraCss' => ['/css/estilos4.css'],
            'treatments' => $promos
        ]);
    }

    public function tratamientos() {
        $treatments = Treatment::getAll(true); // Cargar todos, incluso los inactivos
        $this->render('tratamientos', [
            'title' => 'Tratamientos - Inspire Beauty',
            'extraCss' => ['/css/estilos4.css'],
            'treatments' => $treatments
        ]);
    }

    public function contacto() {
        $this->render('contacto', [
            'title' => 'Contacto - Inspire Beauty',
            'extraCss' => ['/css/estilos2.css']
        ]);
    }

    public function viewEmail() {
        $id = $_GET['id'] ?? '';
        $file = BASE_PATH . '/storage/emails/' . basename($id) . '.html';
        if (file_exists($file)) {
            echo file_get_contents($file);
            exit;
        } else {
            echo "El correo solicitado no existe o ha caducado.";
            exit;
        }
    }

    public function submitContact() {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $mensaje = $_POST['mensaje'] ?? '';

        if (!$nombre || !$email || !$mensaje) {
            $_SESSION['error'] = 'Por favor, rellena los campos obligatorios.';
        } else {
            $db = \Core\Database::getConnection();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $email, $telefono, $mensaje])) {
                $_SESSION['success'] = 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.';
            } else {
                $_SESSION['error'] = 'Hubo un error al enviar el mensaje. Inténtalo de nuevo.';
            }
        }
        header('Location: /contacto');
        exit;
    }
}
