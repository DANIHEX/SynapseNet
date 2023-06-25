<?php

declare(strict_types=1);

namespace synapsenet\core\check;

use synapsenet\core\thread\Thread;

class Status extends Thread {

    /** @var string */
    public string $format;
    /** @var string */
    public string $name;

    /** @var int */
    public int $onlinePlayers;
    /** @var int */
    public int $maxPlayers;

    /** @var string */
    public string $ip;

    /** @var int */
    public int $port;
    /** @var int */
    public int $threads = 0;
    /** @var int */
    public int $memUsage = 0;
    /** @var int */
    public int $cpuUsage = 0;
    /** @var int */
    public int $ticks = 0;

    public function __construct() {
        parent::__construct("StatusWatcher");
    }

    /**
     * @return void
     */
    public function startWaching(): void {
        if($this->onair) {
            return;
        }

        $this->onair = true;
        $this->start(PTHREADS_INHERIT_NONE);
    }

    /**
     * @param string $format
     * @param string $name
     * @param int $onlinePlayers
     * @param int $maxPlayers
     * @param string $ip
     * @param int $port
     * @param int $threads
     * @param int $memUsage
     * @param int $cpuUsage
     *
     * @return void
     */
    public function tick(
        string $format,
        string $name,
        int $onlinePlayers,
        int $maxPlayers,
        string $ip,
        int $port,
        int $threads,
        int $memUsage,
        int $cpuUsage
    ): void {
        $this->format = $format;
        $this->name = $name;
        $this->onlinePlayers = $onlinePlayers;
        $this->maxPlayers = $maxPlayers;
        $this->ip = $ip;
        $this->port = $port;
        $this->threads = $threads;
        $this->memUsage = $memUsage;
        $this->cpuUsage = $cpuUsage;
        $this->ticks++;
    }

    private function tickCliTitle(): void {
        $q = max((min(20, $this->ticks) - 10) * 2, 0);
        $quality = "Low [" . str_repeat(":", $q) . str_repeat(" ", (20 - $q)) . "] High";
        $mem = ((memory_get_usage() + $this->memUsage) / 1024) / 1024;
        // $load = round(($mem / intval(rtrim(ini_get("memory_limit"), "M"))) * 100, 2);

        $title = str_replace([
            "SERVER_NAME",
            "ONLINE_PLAYERS",
            "MAX_PLAYERS",
            "SERVER_IP",
            "SERVER_PORT",
            "SERVER_THREADS",
            "SERVER_TPS",
            "SERVER_MEMORY_USAGE",
            "SERVER_CPU_USAGE",
            "SERVER_QUALITY"
        ], [
            $this->name,
            $this->onlinePlayers,
            $this->maxPlayers,
            $this->ip,
            $this->port,
            $this->threads,
            $this->ticks,
            round($mem, 3) . "MB",
            $this->cpuUsage,
            $quality
        ], $this->format);

        cli_set_process_title($title);
    }

    /**
     * @return void
     */
    private function refresh(): void {
        $this->tickCliTitle();
        $this->ticks = 0;
    }

    /**
     * @return void
     */
    public function run(): void {
        while($this->onair) {
            $this->refresh();
            sleep(1);
        }
    }

}
