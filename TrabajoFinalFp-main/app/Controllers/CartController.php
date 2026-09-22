<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Treatment;
use Core\Database;

class CartController extends Controller {
    public function index() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $cartItems = [];
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $treatment = Treatment::getById($id);
            if ($treatment) {
                $effectivePrice = ($treatment['is_promo'] && $treatment['promo_price']) ? $treatment['promo_price'] : $treatment['price'];
                $treatment['quantity'] = $quantity;
                $treatment['effective_price'] = $effectivePrice;
                $cartItems[] = $treatment;
                $total += $effectivePrice * $quantity;
            }
        }

        $this->render('cart/index', [
            'title' => 'Cesta - Inspire Beauty',
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    public function add() {
        $id = $_POST['treatment_id'] ?? null;
        if ($id) {
            $treatment = Treatment::getById($id);
            if (!$treatment || !$treatment['is_active']) {
                $_SESSION['error'] = 'Este tratamiento ya no está disponible para su compra.';
                header('Location: /cesta');
                exit;
            }

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]++;
            } else {
                $_SESSION['cart'][$id] = 1;
            }
            $_SESSION['success'] = 'Tratamiento añadido a la cesta.';
        }
        header('Location: /cesta');
        exit;
    }

    public function remove() {
        $id = $_POST['treatment_id'] ?? null;
        if ($id && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $_SESSION['success'] = 'Tratamiento eliminado de la cesta.';
        }
        header('Location: /cesta');
        exit;
    }

    public function checkout() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para finalizar la compra.';
            header('Location: /login');
            exit;
        }

        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = 'La cesta está vacía.';
            header('Location: /cesta');
            exit;
        }
        
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $treatment = Treatment::getById($id);
            if (!$treatment || !$treatment['is_active']) {
                $_SESSION['error'] = 'Uno o más tratamientos en tu cesta ya no están disponibles. Han sido eliminados.';
                unset($_SESSION['cart'][$id]);
                header('Location: /cesta');
                exit;
            }
            $effectivePrice = ($treatment['is_promo'] && $treatment['promo_price']) ? $treatment['promo_price'] : $treatment['price'];
            $total += $effectivePrice * $quantity;
        }

        $this->render('cart/checkout', [
            'title' => 'Finalizar Compra - Inspire Beauty',
            'total' => $total
        ]);
    }

    public function processPayment() {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        // Doble validación de disponibilidad
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $treatment = Treatment::getById($id);
            if (!$treatment || !$treatment['is_active']) {
                $_SESSION['error'] = 'Uno o más tratamientos en tu cesta ya no están disponibles. Han sido eliminados.';
                unset($_SESSION['cart'][$id]);
                header('Location: /cesta');
                exit;
            }
        }

        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $clientName = trim($_POST['client_name'] ?? '');
            if (empty($clientName)) $clientName = 'Cliente Anónimo';
            
            $clientEmail = trim($_POST['client_email'] ?? '');
            $paymentMethod = $_POST['payment_method'] ?? 'Efectivo';
            
            // Buscar o crear cliente
            $db = \Core\Database::getConnection();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$clientEmail]);
            $clientUser = $stmt->fetch();
            
            if ($clientUser) {
                $targetUserId = $clientUser['id'];
            } else {
                // Crear usuario invitado
                $stmtInsert = $db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, 'CUENTA_MOSTRADOR', 'client')");
                $stmtInsert->execute([$clientName, $clientEmail]);
                $targetUserId = $db->lastInsertId();
            }

            $_SESSION['pending_payment'] = [
                'card_name' => "Cliente: " . $clientName,
                'card_last_four' => $paymentMethod,
                'target_user_id' => $targetUserId,
                'client_email' => $clientEmail,
                'client_name' => $clientName
            ];
            
            // Los administradores saltan la pantalla de "Procesando"
            header('Location: /checkout/success');
            exit;
            
        } else {
            $cardName = trim($_POST['card_name'] ?? '');
            $cardNumber = trim($_POST['card_number'] ?? '');
            
            if (empty($cardName) || empty($cardNumber)) {
                $_SESSION['error'] = 'Por favor, complete todos los campos de pago.';
                header('Location: /checkout');
                exit;
            }

            // Guardar temporalmente en sesión para registrarlo después del "procesamiento"
            $_SESSION['pending_payment'] = [
                'card_name' => $cardName,
                'card_last_four' => substr($cardNumber, -4)
            ];

            header('Location: /checkout/processing');
            exit;
        }
    }

    public function processingScreen() {
        if (!isset($_SESSION['pending_payment'])) {
            header('Location: /checkout');
            exit;
        }
        
        $this->render('cart/processing', [
            'title' => 'Procesando Pago - Inspire Beauty'
        ]);
    }

    public function successScreen() {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['cart']) || !isset($_SESSION['pending_payment'])) {
            header('Location: /');
            exit;
        }

        $paymentData = $_SESSION['pending_payment'];
        $userId = $paymentData['target_user_id'] ?? $_SESSION['user_id'];
        $db = \Core\Database::getConnection();
        
        try {
            $db->beginTransaction();
            
            // Calcular total
            $total = 0;
            $itemsToInsert = [];
            foreach ($_SESSION['cart'] as $id => $quantity) {
                $treatment = Treatment::getById($id);
                if ($treatment) {
                    $effectivePrice = ($treatment['is_promo'] && $treatment['promo_price']) ? $treatment['promo_price'] : $treatment['price'];
                    $total += $effectivePrice * $quantity;
                    for ($i = 0; $i < $quantity; $i++) {
                        $itemsToInsert[] = [
                            'treatment_id' => $treatment['id'],
                            'price' => $effectivePrice,
                            'name' => $treatment['name']
                        ];
                    }
                }
            }
            
            // Insertar compra
            $stmt = $db->prepare("INSERT INTO purchases (user_id, total_amount) VALUES (?, ?)");
            $stmt->execute([$userId, $total]);
            $purchaseId = $db->lastInsertId();
            
            // Insertar items
            $stmtItem = $db->prepare("INSERT INTO purchase_items (purchase_id, treatment_id, price_at_purchase) VALUES (?, ?, ?)");
            foreach ($itemsToInsert as $item) {
                $stmtItem->execute([$purchaseId, $item['treatment_id'], $item['price']]);
            }

            // Insertar datos de pago
            $paymentData = $_SESSION['pending_payment'];
            $stmtPayment = $db->prepare("INSERT INTO payments (purchase_id, card_name, card_last_four) VALUES (?, ?, ?)");
            $stmtPayment->execute([$purchaseId, $paymentData['card_name'], $paymentData['card_last_four']]);
            
            $db->commit();
            
            // Enviar email de ticket
            $stmtUser = $db->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmtUser->execute([$userId]);
            $user = $stmtUser->fetch();
            $emailId = null;
            if ($user && !empty($user['email'])) {
                $emailId = \Core\EmailService::sendPurchaseTicket(
                    $user['email'], 
                    $user['name'], 
                    $purchaseId, 
                    $total, 
                    $itemsToInsert, 
                    $paymentData['card_last_four']
                );
            }

            // Limpiar cesta y pago pendiente
            $_SESSION['cart'] = [];
            unset($_SESSION['pending_payment']);
            
            $this->render('cart/success', [
                'title' => 'Pago Completado - Inspire Beauty',
                'emailId' => $emailId
            ]);
            
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Error al procesar la compra en base de datos.';
            header('Location: /cesta');
            exit;
        }
    }
}
