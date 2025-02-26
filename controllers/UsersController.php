<?php
require_once __DIR__ . '/../core/View.php';

class UsersController {
    public function list() {
        return View::make('list');
    }
}