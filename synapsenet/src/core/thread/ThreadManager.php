<?php

declare(strict_types=1);

namespace synapsenet\core\thread;

class ThreadManager {

    /** @var Thread[] */
    private static array $pool = [];

    /**
     * @param Thread $thread
     *
     * @return void
     */
    public static function add(Thread $thread): void {
        self::$pool[$thread->getName()] = $thread;
    }

    /**
     * @return void
     */
    public static function shutdownThreads(): void {
        foreach(self::$pool as $thread) {
            $thread->shutdown();
        }
    }

    /**
     * @return Thread[]
     */
    public static function getPool(): array {
        return self::$pool;
    }

    /**
     * @return int
     */
    public static function getCount(): int {
        return count(self::$pool);
    }

    /**
     * @param string $name
     *
     * @return Thread|null
     */
    public static function getThreadByName(string $name): Thread|null {
        if(isset(self::$pool[$name])) {
            return self::$pool[$name];
        }

        return null;
    }
}
