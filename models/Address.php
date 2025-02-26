<?php
require_once __DIR__ . '/BaseModel.php';

class Address extends BaseModel {
    public function __construct() {
        parent::__construct('addresses');
    }
}
