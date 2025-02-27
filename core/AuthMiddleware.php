<?php

require_once __DIR__ . '/../models/User.php';

class AuthMiddleware {
    public static function checkAuthentication($route) {
        $publicRoutes = ['/', '/login', '/cadastrar', '/register'];

        if (in_array($route, $publicRoutes)) {
            return;
        }

        if (!isset($_COOKIE['auth_token'])) {
            header("Location: /?error=1&error_message=Acesso não autorizado! Faça login.");
            exit;
        }

        $userModel = new User();
        $user = $userModel->get([
            'auth_token' => $_COOKIE['auth_token']
        ]);

        if (!$user || strtotime($user['token_expires_at']) < time()) {
            setcookie('auth_token', '', time() - 3600, '/', '', false, true);
            header("Location: /?error=1&error_message=Seu login expirou! Faça login novamente.");
            exit;
        }
    }
}

