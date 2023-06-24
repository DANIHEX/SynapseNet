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

use synapsenet\core\CoreServer;
use synapsenet\core\CoreLogger;

if(version_compare("8.0.0", PHP_VERSION) > 0){
    echo "You need a php version 8.0.0 or higher to use SynapseNet. Your php version:" . PHP_VERSION . PHP_EOL;
    exit(1);
}

$autoloadpath = dirname(__DIR__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
if(!file_exists($autoloadpath)){
    echo "Autoloader not found at " . $autoloadpath;
    exit(1);
}
define("S_AUTOLOAD_PATH", $autoloadpath);
require_once(S_AUTOLOAD_PATH);

echo "Autoload enabled." . PHP_EOL;

define("S_SERVER_PATH", dirname(__DIR__, 2));
define("S_PLAYERS_PATH", S_SERVER_PATH . DIRECTORY_SEPARATOR . "players");
define("S_EXTENSIONS_PATH", S_SERVER_PATH . DIRECTORY_SEPARATOR . "extensions");
define("S_EXTENSIONS_DATA_PATH", S_SERVER_PATH . DIRECTORY_SEPARATOR . "extensions_data");

$logger = new CoreLogger(S_SERVER_PATH . DIRECTORY_SEPARATOR . "server.log");
$core = new CoreServer($logger, S_SERVER_PATH, S_PLAYERS_PATH, S_EXTENSIONS_PATH, S_EXTENSIONS_DATA_PATH);
$core->start();

exit(-1);
