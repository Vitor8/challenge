<?php

$url = "http://localhost:8000/";

if (PHP_OS === 'Darwin') {
    // MacOS
    exec("open $url");
} elseif (PHP_OS === 'Linux') {
    // Linux
    exec("xdg-open $url");
} elseif (PHP_OS === 'WINNT') {
    // Windows
    exec("start $url");
}

echo "✅ Aplicação iniciada! Acesse: $url\n";
