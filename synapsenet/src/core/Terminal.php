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

declare(strict_types = 1);

namespace synapsenet\core;

class Terminal {

    /**
     * @param string $message
     *
     * @return void
     */
    public static function message(string $message): void {
        echo $message . PHP_EOL;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public static function updateTitle(string $title): void {
        if(!cli_set_process_title($title)) {
            CoreServer::getInstance()->getLogger()->error("Could not update title of cli.");
        }
    }

    /**
     * @param $done
     * @param string $message
     * @param int $total
     * @param int $size
     *
     * @return void
     */
    public static function setProgressBar($done, string $message = "Loading...", int $total = 100, int $size = 70): void {
        if($done > $total) return;

        $percent = (double) ($done / $total);
        $bar = floor($percent * $size);
        $pbar = "\r[";
        $pbar .= str_repeat("=", intval($bar));

        if($bar < $size) {
            $pbar .= ">";
            $pbar .= str_repeat(" ", intval($size - $bar));
        } else {
            $pbar .= "=";
        }

        $pbar .= "]  " . $message . str_repeat(" ", 15);

        echo $pbar;

        flush();

        if($done === $total) {
            echo "\n";
        }
    }
}
