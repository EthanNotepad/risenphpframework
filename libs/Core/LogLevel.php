<?php

namespace libs\Core;

/**
 * --------------------------------------------------------------------------------
 * Describes log levels.
 * --------------------------------------------------------------------------------
 * DEBUG – debugging information that reveals the details of the event in detail;
 * INFO – any interesting events. For instance: user has signed in;
 * NOTICE – important events within the expected behavior;
 * WARNING – exceptional cases which are still not errors. For example usage of a deprecated method or wrong API request;
 * ERROR – errors to be monitored, but which don't require an urgent fixing;
 * CRITICAL – critical state or an event. For instance: unavailability of a component or an unhandled exception;
 * ALERT – error and an event to be solved in the shortest time. For example, the database is unavailable;
 * EMERGENCY – whole App/system is completely out of order.
 */
class LogLevel
{
    const EMERGENCY = 'EMERGENCY';
    const ALERT     = 'ALERT';
    const CRITICAL  = 'CRITICAL';
    const ERROR     = 'ERROR';
    const WARNING   = 'WARNING';
    const NOTICE    = 'NOTICE';
    const INFO      = 'INFO';
    const DEBUG     = 'DEBUG';
}
