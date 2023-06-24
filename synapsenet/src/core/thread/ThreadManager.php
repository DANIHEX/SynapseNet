<?php

declare(strict_types = 1);

namespace synapsenet\core\thread;

class ThreadManager {

    private static array $pool = [];

    public static function add(Thread $thread): void {
        self::$pool[$thread->getName()] = $thread;
    }

    public static function shutdownThreads(){
        foreach(self::$pool as $thread){
            $thread->shutdown();
        }
    }

    public static function getPool(): array {
        return self::$pool;
    }

    public static function getCount(): int {
        return count(self::$pool);
    }

    public static function getThreadByName(string $name): Thread|null {
        if(isset(self::$pool[$name])){
            return self::$pool[$name];
        }
        return null;
    }

}
