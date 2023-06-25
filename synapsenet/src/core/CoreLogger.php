<?php

/**
 *
 *  ______   ___   _    _    ____  ____  _____ _   _ _____ _____
 * / ___\ \ / / \ | |  / \  |  _ \/ ___|| ____| \ | | ____|_   _|
 * \___ \\ V /|  \| | / _ \ | |_) \___ \|  _| |  \| |  _|   | |
 *  ___) || | | |\  |/ ___ \|  __/ ___) | |___| |\  | |___  | |
 * |____/ |_| |_| \_/_/   \_\_|   |____/|_____|_| \_|_____| |_|
 *
 *
 *
 *
 */

declare(strict_types=1);

namespace synapsenet\core;

use Threaded;
use Throwable;
use synapsenet\core\thread\Thread;

class CoreLogger extends Thread {

    public const EMERGENCY = "emergency";
    public const ALERT = "alert";
    public const CRITICAL = "critical";
    public const ERROR = "error";
    public const WARNING = "warning";
    public const NOTICE = "notice";
    public const INFO = "info";
    public const DEBUG = "debug";

    /** @var string */
    private string $logfile;

    /** @var Threaded */
    private Threaded $logStream;

    /**
     * @param string $logfile
     */
    public function __construct(string $logfile) {
        parent::__construct("CoreLogger");

        $this->logfile = $logfile;

        $this->logStream = new Threaded();

        $this->onair = true;
        $this->start(PTHREADS_INHERIT_NONE);
    }

    /**
     * @param string $level
     * @param string $message
     *
     * @return void
     */
    public function log(string $level, string $message): void {
        switch($level) {
            case CoreLogger::ALERT:
                $this->alert($message);
                break;
            case CoreLogger::EMERGENCY:
                $this->emergency($message);
                break;
            case CoreLogger::CRITICAL:
                $this->critical($message);
                break;
            case CoreLogger::ERROR:
                $this->error($message);
                break;
            case CoreLogger::WARNING:
                $this->warning($message);
                break;
            case CoreLogger::NOTICE:
                $this->notice($message);
                break;
            case CoreLogger::INFO:
                $this->info($message);
                break;
            case CoreLogger::DEBUG:
                $this->debug($message);
                break;
        }
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function emergency(string $message): void {
        $this->logit($message, CoreLogger::EMERGENCY);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function alert(string $message): void {
        $this->logit($message, CoreLogger::ALERT);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function critical(string $message): void {
        $this->logit($message, CoreLogger::CRITICAL);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function error(string $message): void {
        $this->logit($message, CoreLogger::ERROR);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function warning(string $message): void {
        $this->logit($message, CoreLogger::WARNING);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function notice(string $message): void {
        $this->logit($message, CoreLogger::NOTICE);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function info(string $message): void {
        $this->logit($message, CoreLogger::INFO);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function debug(string $message): void {
        $this->logit($message, CoreLogger::DEBUG);
    }

    /**
     * @param string $message
     * @param string $level
     *
     * @return void
     */
    private function logit(string $message, string $level): void {
        $thread = "System";
        if(Thread::getCurrentThread() !== null) {
            $thread = Thread::getCurrentThread()->getName();
        }

        $prefix = strtoupper($level);
        $fmsg = "[" . $thread . "/" . date("H:i:s") . "][" . $prefix . "] " . $message;

        Terminal::message($fmsg);
        $this->logStream[] = $fmsg . PHP_EOL;
    }

    /**
     * @return void
     */
    public function run(): void {
        $logFile = fopen($this->logfile, "ab");

        while($this->onair) {
            $this->writeLogStream($logFile);
        }

        // This will write waiting log streams to the disk after shutdown
        $this->writeLogStream($logFile);

        fclose($logFile);
    }

    /**
     * @param resource $logResource
     */
    private function writeLogStream($logResource): void {
        while($this->logStream->count() > 0) {
            /** @var string $data */
            $data = $this->logStream->shift();
            fwrite($logResource, $data);
        }
    }

    /**
     * @param Throwable $e
     * @param $trace
     *
     * @return void
     */
    public function logException(Throwable $e, $trace = null) {
        // TODO
    }
}
