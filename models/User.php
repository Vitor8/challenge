<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    public function __construct() {
        parent::__construct('usuarios');
    }
}
