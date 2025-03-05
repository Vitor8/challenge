<?php

require_once __DIR__ . '/BaseModel.php';

/**
 * User Model - Handles user-related database operations.
 */
class User extends BaseModel {
    private const TOKEN_EXPIRATION_TIME = 3600; // 1 hour

    /**
     * Initializes the User model with the associated database table.
     */
    public function __construct() {
        parent::__construct('usuarios');
    }

    /**
     * Saves or removes an authentication token for a user.
     *
     * @param int $userId The ID of the user to associate with the token.
     * @param string|null $token The generated authentication token, or null to remove the token.
     * @return void
     */
    public function saveToken(int $userId, ?string $token): void {
        $this->edit([
            'id' => $userId,
            'auth_token' => $token,
            'token_expires_at' => $token ? $this->generateTokenExpiration() : null
        ]);
    }

    /**
     * Generates a timestamp indicating when the token will expire.
     *
     * @return string The formatted expiration timestamp (Y-m-d H:i:s).
     */
    private function generateTokenExpiration(): string {
        return date('Y-m-d H:i:s', time() + self::TOKEN_EXPIRATION_TIME);
    }
}
