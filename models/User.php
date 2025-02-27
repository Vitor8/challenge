<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    public function __construct() {
        parent::__construct('usuarios');
    }

    public function saveToken($userId, $token) {
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); 
    
        $this->edit([
            'id' => $userId,
            'auth_token' => $token,
            'token_expires_at' => $expiresAt
        ]);
    }
}
