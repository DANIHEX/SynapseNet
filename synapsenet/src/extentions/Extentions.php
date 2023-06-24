<?php

namespace synapsenet\extentions;

class Extentions
{
    protected $name;
    protected $description;
    protected $version;
    protected $author;
    protected $main;

    public function __construct($name, $description, $version, $author, $main)
    {
        $this->name = $name;
        $this->description = $description;
        $this->version = $version;
        $this->author = $author;
        $this->main = $main;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getMain()
    {
        return $this->main;
    }

    public function start()
    {
        $mainFilePath = $this->resolveFilePath($this->main);
        if ($mainFilePath !== false) {
            require_once $mainFilePath;
        }
    }

    protected function resolveFilePath($path)
    {
        $srcPath = 'src/' . str_replace('\\', '/', $path) . '.php';
        if (file_exists($srcPath)) {
            return $srcPath;
        }
        return false;
    }
}
