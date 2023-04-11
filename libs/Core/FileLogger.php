<?php

namespace libs\Core;

class FileLogger
{
    public static function log($level, $message, array $context = []): void
    {
        $dateFormatted = date('Y-m-d H:i:s');

        // Build the message with the current date, log level, 
        // and the string from the arguments
        $message = sprintf(
            '[%s] [%s] %s%s',
            $dateFormatted,
            $level,
            $message,
            PHP_EOL // Line break
        );

        $pathLogs = PROJECT_ROOT_PATH . '/logs';
        createFolder($pathLogs);

        // Use a log file name that includes the user ID if available
        $file = $pathLogs . '/' . 'Daily_' . date('Y_m_d') . '.log';
        file_put_contents($file, $message, FILE_APPEND);

        // FILE_APPEND flag prevents flushing the file content on each call 
        // and simply adds a new string to it
    }

    public static function emergency($message, array $context = []): void
    {
        self::log(LogLevel::EMERGENCY, $message, $context);
    }

    public static function alert($message, array $context = []): void
    {
        self::log(LogLevel::ALERT, $message, $context);
    }

    public static function critical($message, array $context = []): void
    {
        self::log(LogLevel::CRITICAL, $message, $context);
    }

    public static function error($message, array $context = []): void
    {
        self::log(LogLevel::ERROR, $message, $context);
    }

    public static function warning($message, array $context = []): void
    {
        self::log(LogLevel::WARNING, $message, $context);
    }

    public static function notice($message, array $context = []): void
    {
        self::log(LogLevel::NOTICE, $message, $context);
    }

    public static function info($message, array $context = []): void
    {
        self::log(LogLevel::INFO, $message, $context);
    }

    public static function debug($message, array $context = []): void
    {
        self::log(LogLevel::DEBUG, $message, $context);
    }
}
