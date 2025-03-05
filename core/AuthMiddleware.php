<?php

require_once __DIR__ . '/../models/User.php';

class AuthMiddleware {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function checkAuthentication(string $route): void {
        if ($this->isPublicRoute($route)) {
            return;
        }

        if (!$this->isAuthenticated()) {
            $this->redirectWithError("Acesso não autorizado! Faça login.");
        }

        $user = $this->userModel->get(['auth_token' => $_COOKIE['auth_token']]);

        if (!$user || !$this->isTokenValid($user)) {
            setcookie('auth_token', '', time() - 3600, '/', '', false, true);
            $this->redirectWithError("Seu login expirou! Faça login novamente.");
        }
    }

    private function isPublicRoute(string $route): bool {
        $publicRoutes = ['/', '/login', '/cadastrar', '/register'];
        return in_array($route, $publicRoutes, true);
    }

    private function isAuthenticated(): bool {
        return isset($_COOKIE['auth_token']);
    }

    private function isTokenValid(?array $user): bool {
        return $user && strtotime($user['token_expires_at']) > time();
    }

    private function redirectWithError(string $message): void {
        header("Location: /?error=1&error_message=" . urlencode($message));
        exit;
    }
}
