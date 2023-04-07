<?php

namespace libs\Core;

use PDOException;
use libs\Db\DB;

class Logger
{
    /**
     * @Description Record user actions
     * @DateTime 2023-03-24
     * @param int $userId
     * @param string $action
     * @param int $status(1 success, 2 failure)
     * @param string $description
     * @param int $logType(1 web, 2 system)
     * @return void
     */
    private static function log(string $action, int $userId, int $status, string $description, int $logType): void
    {
        $data['user_id'] = $userId;
        $data['description'] = $description;
        $data['action'] = $action;
        $data['status'] = $status;
        $data['log_type'] = $logType;
        $data['request_url'] = $_SERVER['REQUEST_URI'];
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        try {
            DB::link()->table('logs')->insert($data);
        } catch (PDOException $e) {
            // Log the error
            FileLogger::error('Error operating data: ' . $e->getMessage());
        }
    }

    public static function loginin($userId, $status = 1, $description = '', $logType = 1, $itemId = '', $module = ''): void
    {
        if (empty($description)) {
            if ($status == 1) {
                $description = 'User login successful';
            } else {
                $description = 'User login failed';
            }
        }
        self::log(LogAction::LOGININ, (int)$userId, (int)$status, $description, (int)$logType);
    }

    public static function loginout($userId, $status = 1, $description = '', $logType = 1, $itemId = '', $module = ''): void
    {
        if (empty($description)) {
            if ($status == 1) {
                $description = 'User logged out successfully';
            } else {
                $description = 'User logout failed';
            }
        }
        self::log(LogAction::LOGINOUT, (int)$userId, (int)$status, $description, (int)$logType);
    }

    public static function add($userId, $status = 1, $description = '', $logType = 1, $itemId = '', $module = ''): void
    {
        if (empty($description)) {
            if ($status == 1) {
                $description = "The user has successfully created an {$module}, and the {$module} ID is {$itemId}";
            } else {
                $description = "User creation {$module} failed";
            }
        }
        self::log(LogAction::ADD, (int)$userId, (int)$status, $description, (int)$logType);
    }

    public static function update($userId, $status = 1, $description = '', $logType = 1, $itemId = '', $module = ''): void
    {
        if (empty($description)) {
            if ($status == 1) {
                if ($module == 'password') {
                    $description = "The user has changed password successfully";
                } else {
                    $description = "The user has successfully updated the {$module}, and the {$module} ID is {$itemId}";
                }
            } else {
                if ($module == 'password') {
                    $description = "The user failed to change password";
                } else {
                    $description = "User update {$module} failed";
                }
            }
        }
        self::log(LogAction::UPDATE, (int)$userId, (int)$status, $description, (int)$logType);
    }

    public static function delete($userId, $status = 1, $description = '', $logType = 1, $itemId = '', $module = ''): void
    {
        if (empty($description)) {
            if ($status == 1) {
                $description = "The user has successfully deleted the {$module}, and the {$module} ID is {$itemId}";
            } else {
                $description = "User deletion {$module} failed";
            }
        }
        self::log(LogAction::DELETE, (int)$userId, (int)$status, $description, (int)$logType);
    }

    public static function sensitive($userId, $status = 1, $description = '', $logType = 1, $itemId = '', $module = ''): void
    {
        if (empty($description)) {
            $description = "The user has performed a sensitive operation, and the operation behavior is: {$module}";
        }
        self::log(LogAction::SENSITIVE, (int)$userId, (int)$status, $description, (int)$logType);
    }
}
