<?php

/**
 * Opens the application URL in the default web browser based on the operating system.
 */
class ApplicationLauncher {
    /**
     * Opens the specified URL in the system's default web browser.
     *
     * @param string $url The URL to open.
     * @return void
     */
    public static function openUrl(string $url): void {
        switch (PHP_OS_FAMILY) {
            case 'Darwin': // macOS
                exec("open $url");
                break;
            case 'Linux': // Linux
                exec("xdg-open $url");
                break;
            case 'Windows': // Windows
                exec("start $url");
                break;
            default:
                echo "⚠️ Unable to open URL automatically. Please open manually: $url\n";
                return;
        }

        echo "✅ Application started! Access: $url\n";
    }
}

// Define the application URL
$applicationUrl = "http://localhost:8000/";

// Open the application in the browser
ApplicationLauncher::openUrl($applicationUrl);
