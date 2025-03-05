<?php

require_once __DIR__ . '/../models/User.php';

class AuthMiddleware {
    private User $userModel;

    /**
     * Initializes the AuthMiddleware with the User model.
     */
    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Checks if the user is authenticated before allowing access to a protected route.
     *
     * @param string $route The requested route.
     * @return void Redirects if authentication fails.
     */
    public function checkAuthentication(string $route): void {
        if ($this->isPublicRoute($route)) {
            return;
        }

        if (!$this->isAuthenticated()) {
            $this->redirectWithError("Unauthorized access! Please log in.");
        }

        $user = $this->userModel->get(['auth_token' => $_COOKIE['auth_token']]);

        if (!$user || !$this->isTokenValid($user)) {
            setcookie('auth_token', '', time() - 3600, '/', '', false, true);
            $this->redirectWithError("Your session has expired! Please log in again.");
        }
    }

    /**
     * Determines whether a given route is publicly accessible.
     *
     * @param string $route The requested route.
     * @return bool True if the route is public, false otherwise.
     */
    private function isPublicRoute(string $route): bool {
        $publicRoutes = ['/', '/login', '/cadastrar', '/register'];
        return in_array($route, $publicRoutes, true);
    }

    /**
     * Checks if the user has an active authentication token.
     *
     * @return bool True if the user is authenticated, false otherwise.
     */
    private function isAuthenticated(): bool {
        return isset($_COOKIE['auth_token']);
    }

    /**
     * Validates if the authentication token is still valid (not expired).
     *
     * @param array|null $user The user data retrieved from the database.
     * @return bool True if the token is valid, false otherwise.
     */
    private function isTokenValid(?array $user): bool {
        return $user && strtotime($user['token_expires_at']) > time();
    }

    /**
     * Redirects the user to the login page with an error message.
     *
     * @param string $message The error message to display.
     * @return void Stops execution and redirects the user.
     */
    private function redirectWithError(string $message): void {
        header("Location: /?error=1&error_message=" . urlencode($message));
        exit;
    }
}
