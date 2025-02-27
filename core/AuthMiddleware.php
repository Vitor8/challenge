<?php
session_start();

class AuthMiddleware {
    public static function checkAuthentication($route) {
        $publicRoutes = ['/', '/login', '/cadastrar', '/register'];

        if (in_array($route, $publicRoutes)) {
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: /?error=1&error_message=Acesso não autorizado! Faça login.");
            exit;
        }
    }
}
