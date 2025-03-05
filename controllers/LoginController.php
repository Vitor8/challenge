<?php

require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/User.php';

class LoginController {
    private User $userModel;

    /**
     * Initializes the LoginController with the User model.
     */
    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Displays the login page.
     *
     * @return string The rendered login view.
     */
    public function home(): string {
        $request = new Request();

        return View::make('index', [
            'error' => $request->query('error'),
            'error_message' => $request->query('error_message')
        ]);
    }

    /**
     * Handles user authentication.
     *
     * @return void Redirects to the client list if successful, otherwise returns an error.
     */
    public function login(): void {
        $request = new Request();
        $login = $request->input('login');
        $password = $request->input('password');

        $validationError = $this->validateLoginFields($login, $password);
        if ($validationError) {
            View::redirect('/', $validationError);
            return;
        }

        $user = $this->userModel->get(['login' => $login]);
        if (!$user || !password_verify($password, $user['password'])) {
            View::redirect('/', [
                'error' => true,
                'error_message' => 'Incorrect username or password!'
            ]);
            return;
        }

        $token = bin2hex(random_bytes(32));
        setcookie('auth_token', $token, time() + 3600, '/', '', false, true);
        $this->userModel->saveToken($user['id'], $token);

        View::redirect('/clientes');
    }

    /**
     * Displays the user registration page.
     *
     * @return string The rendered registration view.
     */
    public function registerView(): string {
        $request = new Request();

        return View::make('register', [
            'error' => $request->query('error'),
            'error_message' => $request->query('error_message')
        ]);
    }

    /**
     * Handles user registration.
     *
     * @return void Redirects to the login page with a success message, or an error if registration fails.
     */
    public function register(): void {
        $request = new Request();
        $login = $request->input('login');
        $password = $request->input('password');
        $confirmPassword = $request->input('confirm_password');

        $validationError = $this->validateRegisterFields($login, $password, $confirmPassword);
        if ($validationError) {
            View::redirect('/cadastrar', $validationError);
            return;
        }

        $existingUser = $this->userModel->get(['login' => $login]);
        if ($existingUser) {
            View::redirect('/cadastrar', [
                'error' => true,
                'error_message' => 'A user with this username already exists. Please choose another one!'
            ]);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->userModel->create([
            'login' => $login,
            'password' => $hashedPassword
        ]);

        View::redirect('/', [
            'success' => true,
            'success_message' => 'User successfully registered!'
        ]);
    }

    /**
     * Validates login fields before authentication.
     *
     * @param string|null $login The username entered by the user.
     * @param string|null $password The password entered by the user.
     * @return array|null An error message array if validation fails, otherwise null.
     */
    private function validateLoginFields(?string $login, ?string $password): ?array {
        if (empty($login) || empty($password)) {
            return [
                'error' => true,
                'error_message' => 'Fill in all required fields!'
            ];
        }
        return null;
    }

    /**
     * Validates registration fields before creating a new user.
     *
     * @param string|null $login The username entered by the user.
     * @param string|null $password The password entered by the user.
     * @param string|null $confirmPassword The password confirmation entered by the user.
     * @return array|null An error message array if validation fails, otherwise null.
     */
    private function validateRegisterFields(?string $login, ?string $password, ?string $confirmPassword): ?array {
        if (empty($login) || empty($password) || empty($confirmPassword)) {
            return [
                'error' => true,
                'error_message' => 'Fill in all required fields!'
            ];
        }

        if ($password !== $confirmPassword) {
            return [
                'error' => true,
                'error_message' => 'Passwords do not match!'
            ];
        }

        return null;
    }

    /**
     * Logs out the authenticated user by invalidating their authentication token.
     *
     * This method checks if the user is authenticated via the `auth_token` cookie.
     * If the user is found in the database, their authentication token is cleared.
     * The cookie storing the auth token is also deleted.
     *
     * @return void Outputs a JSON response indicating success or failure.
     */
    public function logout(): void {
        if (!isset($_COOKIE['auth_token'])) {
            echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
            return;
        }

        $user = $this->userModel->get(['auth_token' => $_COOKIE['auth_token']]);
        if ($user) {
            $this->userModel->saveToken($user['id'], null);
        }

        setcookie('auth_token', '', time() - 3600, '/', '', false, true);

        echo json_encode(['status' => 'success', 'message' => 'Logout realizado com sucesso.']);
    }
 
}
