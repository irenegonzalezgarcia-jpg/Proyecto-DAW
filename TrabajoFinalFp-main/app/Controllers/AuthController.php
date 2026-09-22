<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            $user = User::findByEmail($email);
            
            if ($user && $user['password_hash'] === 'CUENTA_MOSTRADOR') {
                $_SESSION['pending_setup_email'] = $email;
                $_SESSION['pending_setup_name'] = $user['name'];
                header('Location: /completar-registro');
                exit;
            }

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    header('Location: /admin');
                } else {
                    header('Location: /panel');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas.';
            }
        }
        
        $this->render('login', [
            'title' => 'Iniciar Sesión - Inspire Beauty',
            'extraCss' => ['/css/estilos3.css']
        ]);
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $apellidos = $_POST['apellidos'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            
            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Las contraseñas no coinciden.';
            } else {
                $existingUser = User::findByEmail($email);
                if ($existingUser) {
                    if ($existingUser['password_hash'] === 'CUENTA_MOSTRADOR') {
                        // Es una cuenta de mostrador, redirigir a completar registro
                        $_SESSION['pending_setup_email'] = $email;
                        $_SESSION['pending_setup_name'] = $existingUser['name'];
                        header('Location: /completar-registro');
                        exit;
                    } else {
                        $_SESSION['error'] = 'El email ya está registrado. Por favor, inicie sesión.';
                    }
                } else {
                    $fullName = trim($nombre . ' ' . $apellidos);
                    if (User::create($fullName, $email, $password, $telefono)) {
                        $_SESSION['success'] = 'Cuenta creada con éxito. Ya puedes iniciar sesión.';
                        header('Location: /login');
                        exit;
                    } else {
                        $_SESSION['error'] = 'Ocurrió un error al registrar.';
                    }
                }
            }
        }
        
        $this->render('registro', [
            'title' => 'Registro - Inspire Beauty',
            'extraCss' => ['/css/estilos3.css']
        ]);
    }
    
    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function completeRegister() {
        if (!isset($_SESSION['pending_setup_email'])) {
            header('Location: /registro');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $apellidos = $_POST['apellidos'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $email = $_SESSION['pending_setup_email'];

            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Las contraseñas no coinciden.';
            } else {
                $fullName = trim($nombre . ' ' . $apellidos);
                $db = \Core\Database::getConnection();
                $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, password_hash = ? WHERE email = ? AND password_hash = 'CUENTA_MOSTRADOR'");
                if ($stmt->execute([$fullName, $telefono, password_hash($password, PASSWORD_DEFAULT), $email])) {
                    unset($_SESSION['pending_setup_email']);
                    unset($_SESSION['pending_setup_name']);
                    $_SESSION['success'] = 'Registro completado con éxito. Ya puedes iniciar sesión.';
                    header('Location: /login');
                    exit;
                } else {
                    $_SESSION['error'] = 'Ocurrió un error al completar el registro.';
                }
            }
        }

        $this->render('auth/complete_register', [
            'title' => 'Completar Registro - Inspire Beauty',
            'extraCss' => ['/css/estilos3.css']
        ]);
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $user = User::findByEmail($email);
            
            if ($user) {
                $_SESSION['reset_email'] = $email;
                header('Location: /restablecer-password');
                exit;
            } else {
                $_SESSION['error'] = 'No existe ninguna cuenta asociada a este correo electrónico.';
            }
        }
        
        $this->render('auth/forgot_password', [
            'title' => 'Recuperar Contraseña - Inspire Beauty',
            'extraCss' => ['/css/estilos3.css']
        ]);
    }

    public function resetPassword() {
        if (!isset($_SESSION['reset_email'])) {
            header('Location: /recuperar-password');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $email = $_SESSION['reset_email'];

            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Las contraseñas no coinciden.';
            } else {
                $db = \Core\Database::getConnection();
                $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
                if ($stmt->execute([password_hash($password, PASSWORD_DEFAULT), $email])) {
                    unset($_SESSION['reset_email']);
                    $_SESSION['success'] = 'Contraseña actualizada con éxito. Ya puedes iniciar sesión.';
                    header('Location: /login');
                    exit;
                } else {
                    $_SESSION['error'] = 'Ocurrió un error al actualizar la contraseña.';
                }
            }
        }

        $this->render('auth/reset_password', [
            'title' => 'Reestablecer Contraseña - Inspire Beauty',
            'extraCss' => ['/css/estilos3.css']
        ]);
    }
}
