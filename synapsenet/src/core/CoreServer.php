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
use synapsenet\player\Gamemode;
use synapsenet\core\check\Status;
use synapsenet\core\check\SystemUsage;
use synapsenet\core\thread\ThreadManager;
use synapsenet\network\Network;
use synapsenet\network\Query;

class CoreServer {

    public const PERFORMANCE_NORMAL = 0;
    public const PERFORMANCE_TURBO = 1;
    public const PERFORMANCE_THUNDER = 2;
    public const PERFORMANCE_MAX = 3;

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

    /** @var Status */
    public Status $status;
    
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
    public int $defaultGameMode = Gamemode::SURVIVAL;

    /** @var int */
    public int $maxPlayers;

    /** @var array */
    public array $onlinePlayers = [];

    /** @var int */
    public int $performanceMode;

    /** @var array */
    private array $properties = [];

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
        if(!file_exists($propertiesPath)) {
            copy($serverPath . DIRECTORY_SEPARATOR . "synapsenet" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "server.yml", $serverPath . DIRECTORY_SEPARATOR . "server.yml");
        }

        $this->properties = yaml_parse_file($propertiesPath);

        $this->ip = "0.0.0.0";
        $this->ip6 = "::";
        $this->port = $this->getProperty("port");
        $this->port6 = $this->getProperty("port6");

        $this->name = $this->getProperty("server-name");

        $this->motd = $this->getProperty("motd");
        $this->motq = $this->getProperty("motq");

        $this->defaultGameMode = $this->getProperty("default-gamemode");

        $this->maxPlayers = $this->getProperty("max-players");

        $this->performanceMode = min(max($this->getProperty("performance-mode"), CoreServer::PERFORMANCE_NORMAL), CoreServer::PERFORMANCE_MAX);

        $this->status = new Status();
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

        return min(max($mode, Gamemode::SURVIVAL), Gamemode::SPECTATOR);
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
     * @return Status
     */
    public function getStatus(): Status {
        return $this->status;
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
        return $this->onlinePlayers;
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int {
        return $this->maxPlayers;
    }

    /**
     * @param bool $asString
     *
     * @return int|string
     */
    public function getPerformanceMode(bool $asString = false): int|string {
        $mode = $this->performanceMode;
        if($asString) {
            return match ($mode) {
                CoreServer::PERFORMANCE_TURBO => "Turbo",
                CoreServer::PERFORMANCE_THUNDER => "Thunder",
                CoreServer::PERFORMANCE_MAX => "Max",
                default => "Normal",
            };
        }

        return $mode;
    }

    /**
     * @return void
     */
    public function start(): void {
        if($this->onair) {
            return;
        }

        Terminal::setProgressBar(100, "Done!");
        $this->network->start();
        $this->logger->info("Server started on " . $this->getIp() . ":" . $this->getPort());
        $this->status->startWaching();
        $this->onair = true;
        $this->proccess();
    }

    /**
     * @return void
     */
    private function proccess(): void {
        $this->nextTick = microtime(true);

        while($this->onair) {
            $this->tick();
        }
    }

    /**
     * @return void
     */
    private function tick(): void {
        $tickTime = microtime(true);
        if($this->getPerformanceMode() !== CoreServer::PERFORMANCE_MAX) {
            if(($tickTime - $this->nextTick) < -0.025) {
                return;
            }
        }

        $this->status->tick($this->getProperty("cli-title-format"), $this->getName(), count($this->getOnlinePlayers()), $this->getMaxPlayers(), $this->getIp(), $this->getPort(), ThreadManager::getCount(), memory_get_usage(), SystemUsage::getCpuUsage());

        $this->query->generateQuery($this->getName(), count($this->getOnlinePlayers()), $this->getMaxPlayers(), $this->getServerUid(), $this->getMotd(), $this->getDefaultGameMode(true), $this->getDefaultGameMode(), $this->getPort(), $this->getPort6());

        $this->network->getPacketHandler()->proccess();


        if($this->performanceMode !== CoreServer::PERFORMANCE_MAX) {
            if(($this->nextTick - $tickTime) < -1) {
                $this->nextTick = $tickTime;
            } else {
                $amount = match ($this->performanceMode) {
                    CoreServer::PERFORMANCE_TURBO => 0.005,
                    CoreServer::PERFORMANCE_THUNDER => 0.0005,
                    default => 0.05
                };

                $this->nextTick += $amount;
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

        ThreadManager::shutdownThreads();

        $this->onair = false;
        // TODO: Shutdown cores and process
    }
}
