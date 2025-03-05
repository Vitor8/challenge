<?php

class View {
    /**
     * Loads and renders a view file with the provided data.
     *
     * @param string $viewPath The path of the view file (without the .php extension).
     * @param array $data Optional associative array of data to be extracted into the view.
     * @return string The rendered view content as a string.
     */
    public static function make(string $viewPath, array $data = []): string {
        extract($data);
        ob_start();
        require __DIR__ . "/../frontend/$viewPath.php";
        return ob_get_clean();
    }

    /**
     * Redirects the user to a specified URL with optional query parameters.
     *
     * @param string $url The destination URL.
     * @param array $data Optional associative array of query parameters to append to the URL.
     * @return void
     */
    public static function redirect(string $url, array $data = []): void {
        $queryString = http_build_query($data);
        header("Location: $url" . (!empty($queryString) ? "?$queryString" : ""));
        exit;
    }
}
