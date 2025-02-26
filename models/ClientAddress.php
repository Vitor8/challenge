<?php
require_once __DIR__ . '/BaseModel.php';

class ClientAddress extends BaseModel {
    public function __construct() {
        parent::__construct('client_address');
    }
}
