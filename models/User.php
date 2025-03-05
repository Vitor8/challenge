<?php

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    private const TOKEN_EXPIRATION_TIME = 3600; // 1 hour

    public function __construct() {
        parent::__construct('usuarios');
    }

    /**
     * Saves an authentication token for a user with an expiration time.
     */
    public function saveToken(int $userId, string $token): void {
        $this->edit([
            'id' => $userId,
            'auth_token' => $token,
            'token_expires_at' => $this->generateTokenExpiration()
        ]);
    }

    /**
     * Generates the token expiration timestamp.
     */
    private function generateTokenExpiration(): string {
        return date('Y-m-d H:i:s', time() + self::TOKEN_EXPIRATION_TIME);
    }
}
