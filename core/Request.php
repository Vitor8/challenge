<?php

class Request {
    private $get;
    private $post;

    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    public function input($key, $default = null) {
        return $this->post[$key] ?? $default;
    }

    public function query($key, $default = null) {
        return $this->get[$key] ?? $default;
    }

    public function all() {
        return ['get' => $this->get, 'post' => $this->post];
    }
}
