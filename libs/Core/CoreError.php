<?php

namespace libs\Core;

class CoreError
{
    protected $debug;

    public function __construct($debug = true)
    {
        $this->debug = $debug;
    }

    public function bootstrap()
    {
        // Set error reporting to report all errors except notices
        error_reporting(E_ALL & ~E_NOTICE);

        // Set the error and exception handlers
        set_error_handler([$this, 'handleError'], E_ALL | E_STRICT);
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        // Check if error reporting is turned off or the error is suppressed with an @ symbol
        if (!(error_reporting() && $errno)) {
            return;
        }
        // Handle the error based on its severity
        switch ($errno) {
            case E_USER_ERROR:
                $errorType = 'Fatal Error';
                break;

            case E_USER_WARNING:
                $errorType = 'Warning';
                break;

            default:
                $errorType = 'Error';
                break;
        }

        // Build the error message
        $errorMessage = "$errorType: $errstr in $errfile on line $errline";

        // Log the error to a file or database
        FileLogger::error($errorMessage);

        // If debugging is enabled, output the error message to the screen
        if ($this->debug) {
            echo "<p>$errorMessage</p>";
        } else {
            $defaultErrorMessage = Config::get('app.defaultErrorMessage');
            echo "<p>$defaultErrorMessage</p>";
        }

        // If it's a fatal error, stop the script from executing
        if ($errno == E_USER_ERROR) {
            exit(1);
        }
    }

    public function handleException($e)
    {
        // Build the error message
        $errorMessage = "Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();

        // Log the error to a file or database
        FileLogger::error($errorMessage);

        // If debugging is enabled, output the error message to the screen
        if ($this->debug) {
            echo "<p>$errorMessage</p>";
        } else {
            $defaultErrorMessage = Config::get('app.defaultErrorMessage');
            echo "<p>$defaultErrorMessage</p>";
        }

        // Stop the script from executing
        exit(1);
    }
}
