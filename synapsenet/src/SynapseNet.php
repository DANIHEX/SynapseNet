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

namespace synapsenet {

    use synapsenet\core\CoreServer;
    use synapsenet\core\CoreLogger;
    use synapsenet\core\Terminal;

    const MIN_PHP_VERSION = "8.0.0";

    if(version_compare(MIN_PHP_VERSION, PHP_VERSION) > 0){
        message("ERROR", "You need a php version " . MIN_PHP_VERSION . " or higher to use SynapseNet. Your php version:" . PHP_VERSION);
        exit(1);
    }

    function message(string $prefix, string $message): void {
        echo "[SynapseNet@BootCore][" . strtoupper($prefix) . "@" . date("Y/m/d H:i:s") . "] " . $message . PHP_EOL;
    }

    function check(): array { // TODO: Check requirements
        $errors = [];
        // DO
        return $errors;
    }

    function boot(): void {
        if(check()){
            message("critical", "Some requirement are not getting satisfied.");
        }

        $autoloaderpath = dirname(__DIR__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
        // echo $autoloaderpath;
        if(!file_exists($autoloaderpath)){
            message("critical", "Autoloader not found at " . $autoloaderpath);
            exit(1);
        }
        define("synapsenet\AUTOLOADER_PATH", $autoloaderpath);
        require_once(AUTOLOADER_PATH);

        Terminal::setProgressBar(5, "Auto load enabled...");

        define("synapsenet\SERVER_PATH", dirname(__DIR__, 2));
        define("synapsenet\PLAYERS_PATH", SERVER_PATH . DIRECTORY_SEPARATOR . "players");
        define("synapsenet\EXTENSIONS_PATH", SERVER_PATH . DIRECTORY_SEPARATOR . "extensions");
        define("synapsenet\EXTENSIONS_DATA_PATH", SERVER_PATH . DIRECTORY_SEPARATOR . "extensions_data");

        Terminal::setProgressBar(10, "Data folders created...");

        $logger = new CoreLogger(SERVER_PATH . DIRECTORY_SEPARATOR . "server.log");
        $core = new CoreServer($logger, SERVER_PATH, PLAYERS_PATH, EXTENSIONS_PATH, EXTENSIONS_DATA_PATH);
        $core->start();
    }

    boot();
    exit(-1);

}
