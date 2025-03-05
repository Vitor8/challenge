<?php

class Request {
    private array $get;
    private array $post;

    /**
     * Initializes request data from $_GET and $_POST superglobals.
     */
    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    /**
     * Retrieves a value from the POST request.
     *
     * @param string $key The key to retrieve from the POST data.
     * @param mixed|null $default The default value if the key does not exist.
     * @return mixed The value from the POST request or the default value.
     */
    public function input(string $key, mixed $default = null): mixed {
        return $this->post[$key] ?? $default;
    }

    /**
     * Retrieves a value from the GET request.
     *
     * @param string $key The key to retrieve from the GET data.
     * @param mixed|null $default The default value if the key does not exist.
     * @return mixed The value from the GET request or the default value.
     */
    public function query(string $key, mixed $default = null): mixed {
        return $this->get[$key] ?? $default;
    }

    /**
     * Returns all GET and POST request data.
     *
     * @return array The GET and POST data as an associative array.
     */
    public function all(): array {
        return ['get' => $this->get, 'post' => $this->post];
    }
}
