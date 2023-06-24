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

use Exception;
use synapsenet\core\check\StatusWatcher;
use synapsenet\core\thread\ThreadManager;
use synapsenet\network\Network;
use synapsenet\network\Query;

class CoreServer {

    /** @var CoreServer|null */
    public static ?CoreServer $instance = null;

    /** @var bool */
    public bool $onair = false;

    /** @var string */
    public string $serverPath;
    /** @var string */
    public string $playersDir;
    /** @var string */
    public string $extensionsDir;
    /** @var string */
    public string $extensionsDataDir;

    /** @var CoreLogger */
    public CoreLogger $logger;

    /** @var Query */
    public Query $query;

    /** @var Network */
    public Network $network;

    /** @var StatusWatcher */
    public StatusWatcher $statusWatcher;
    
    /** @var string */
    public string $name;

    /** @var string */
    public string $uid = "abef4718-4d4d-5cc7-1543-73f43287aebc"; // temp

    /** @var string */
    public string $motd;
    /** @var string */
    public string $motq;

    /** @var string */
    public string $ip;
    /** @var string */
    public string $ip6;

    /** @var int */
    public int $port;
    /** @var int */
    public int $port6;

    /** @var int */
    public int $defaultGameMode = 0;

    /** @var int */
    public int $maxPlayers;

    /** @var array */
    public array $onlinePlayers = [];

    /** @var array */
    private array $properties = [];

    /** @var int */
    private int $ticks = 0;

    /** @var float */
    private float $tickCycleTime = 0.0;
    /** @var float */
    private float $nextTick = 0.0;

    /**
     * @param CoreLogger $logger
     * @param string $serverPath
     * @param string $playersDir
     * @param string $extensionsDir
     * @param string $extensionsDataDir
     *
     * @throws Exception
     */
    public function __construct(CoreLogger $logger, string $serverPath, string $playersDir, string $extensionsDir, string $extensionsDataDir) {
        Terminal::setProgressBar(30, "Preparing...");
        if(self::$instance instanceof CoreServer) {
            $logger->critical("Core server instance is created before.");
            return;
        }
        self::$instance = $this;
        $this->serverPath = $serverPath;
        $this->logger = $logger;
        if(!is_dir($playersDir)) {
            mkdir($playersDir);
        }
        if(!is_dir($extensionsDir)) {
            mkdir($extensionsDir);
        }
        if(!is_dir($extensionsDataDir)) {
            mkdir($extensionsDataDir);
        }

        $this->playersDir = $playersDir;
        $this->extensionsDir = $extensionsDir;
        $this->extensionsDataDir = $extensionsDataDir;

        $propertiesPath = $serverPath . DIRECTORY_SEPARATOR . "server.yml";
        copy($serverPath . DIRECTORY_SEPARATOR . "synapsenet" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "server.yml", $serverPath . DIRECTORY_SEPARATOR . "server.yml");
        $this->properties = yaml_parse_file($propertiesPath);

        $this->ip = "0.0.0.0";
        $this->ip6 = "::";
        $this->port = $this->getProperty("port");
        $this->port6 = $this->getProperty("port6");

        $this->name = $this->getProperty("server-name");

        $this->motd = $this->getProperty("motd");
        $this->motq = $this->getProperty("motq");

        $this->defaultGameMode = $this->getProperty("default-gamemode");

        $this->setMaxPlayers($this->getProperty("max-players"));

        $this->statusWatcher = new StatusWatcher();
        $this->query = new Query();
        $this->network = new Network($this, $this->ip, $this->ip6, $this->port, $this->port6);

        usleep(1000000); // Simulate load job

        Terminal::setProgressBar(50, "Starting...");
    }

    /**
     * @return CoreServer
     */
    public static function getInstance(): CoreServer {
        return self::$instance;
    }

    /**
     * @param string $property
     *
     * @return mixed
     * @throws Exception
     */
    public function getProperty(string $property): mixed {
        if(isset($this->properties[$property])) {
            return $this->properties[$property];
        }

        throw new Exception("Could not find property: " . $property);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIp(): string {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getIp6(): string {
        return $this->ip6;
    }

    /**
     * @return int
     */
    public function getPort(): int {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getPort6(): int {
        return $this->port6;
    }

    /**
     * @return string
     */
    public function getServerUid(): string {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getMotd(): string {
        return $this->motd;
    }

    /**
     * @return string
     */
    public function getMotq(): string {
        return $this->motq;
    }

    /**
     * @param bool $asString
     *
     * @return int|string
     */
    public function getDefaultGameMode(bool $asString = false): int|string {
        $mode = $this->defaultGameMode;
        if($asString) {
            return match ($mode) {
                1 => "Creative",
                2 => "Adventure",
                3 => "Spectator",
                default => "Survival",
            };
        }

        return min(max($mode, 0), 3);
    }

    /**
     * @return CoreLogger
     */
    public function getLogger(): CoreLogger {
        return $this->logger;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query {
        return $this->query;
    }

    /**
     * @return Network
     */
    public function getNetwork(): Network {
        return $this->network;
    }

    /**
     * @return StatusWatcher
     */
    public function getStatusWatcher(): StatusWatcher {
        return $this->statusWatcher;
    }

    /**
     * @param int $max
     *
     * @return void
     */
    private function setMaxPlayers(int $max): void {
        $this->maxPlayers = $max;
    }

    /**
     * @return array
     */
    public function getOnlinePlayers(): array {
        if(count($this->onlinePlayers) <= 0) {
            return [];
        }

        return $this->onlinePlayers;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int {
        return $this->maxPlayers;
    }

    /**
     * @return void
     */
    public function start(): void {
        if($this->onair) return;
        $this->getNetwork()->start();
        $this->getStatusWatcher()->startWaching();
        Terminal::setProgressBar(100, "Done!");
        $this->onair = true;
        $this->proccess();
    }

    private function proccess() {
        $this->tickCycleTime = microtime(true);
        $this->nextTick = microtime(true);

        while($this->onair) {
            $this->tick();
            $this->sleepUntilNextTick($this->nextTick);
        }
    }

    /**
     * @return void
     */
    private function tick(): void {
        $tickTime = microtime(true);
        if(($tickTime - $this->nextTick) < -0.025) {
            return;
        }

        // Check the time consumed to cycle full 20 ticks in second(s)
        // if($this->ticks >= 20) {
        //     $cycleTime = microtime(true) - $this->tickCycleTime;
        //     $this->logger->info("Elapsed: " . $cycleTime);
        //     $this->tickCycleTime = microtime(true);
        //     $this->ticks = 0;
        // }


        $this->getStatusWatcher()->tick($this->getProperty("cli-title-format"), $this->getName(), count($this->getOnlinePlayers()), $this->getMaxPlayers(), $this->getIp(), $this->getPort(), ThreadManager::getCount());

        $this->getQuery()->generateQuery($this->getName(), count($this->getOnlinePlayers()), $this->getMaxPlayers(), $this->getServerUid(), $this->getMotd(), $this->getDefaultGameMode(true), $this->getDefaultGameMode(), $this->getPort(), $this->getPort6());

        $this->getNetwork()->getPacketHandler()->proccess();


        if(($this->nextTick - $tickTime) < -1) {
            $this->nextTick = $tickTime;
        } else {
            $this->nextTick += 0.05;
        }
        $this->ticks++;
    }

    /**
     * @param float $time
     *
     * @return void
     */
    private function sleepUntilNextTick(float $time): void {
        while(true) {
            $sleepTime = (int) (($time - microtime(true)) * 1000000);
            if($sleepTime > 0) {
                usleep($sleepTime);
            } else {
                break;
            }
        }
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function shutdown(string $message = ""): void {
        $this->logger->info("Shutting down...");

        if($message !== "") {
            $this->logger->info($message);
        }

        $this->logger->shutdown();

        $this->onair = false;
        // TODO: Shutdown cores and process
    }
}
