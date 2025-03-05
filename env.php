<?php

/**
 * Loads environment variables from the `.env` file into the PHP environment.
 */
class EnvLoader {
    /**
     * Reads and sets environment variables from the .env file.
     *
     * @return void
     */
    public static function load(): void {
        $envFilePath = __DIR__ . '/.env';

        if (!file_exists($envFilePath)) {
            return;
        }

        $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            putenv($line);
        }
    }
}

// Load environment variables
EnvLoader::load();
