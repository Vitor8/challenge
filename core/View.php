<?php

class View {
    public static function make($viewPath, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . "/../frontend/$viewPath.php";
        return ob_get_clean();
    }

    public static function redirect($url, $data = []) {
        $queryString = http_build_query($data);
        header("Location: $url" . (!empty($queryString) ? "?$queryString" : ""));
        exit;
    }
}
