<?php
require_once __DIR__ . '/BaseModel.php';

class Client extends BaseModel {
    public function __construct() {
        parent::__construct('clients');
    }
}
