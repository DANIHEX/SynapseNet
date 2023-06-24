<?php

namespace synapsenet\extentions;

class ExtentionLoader
{
    protected $Extentions = [];

    public function loadExtention($directory)
    {
        $configFile = $directory . '/extension.yml';
        if (!file_exists($configFile)) {
            return false;
        }

        $config = yaml_parse_file($configFile);
        if (!$config) {
            return false;
        }

        $name = $config['name'] ?? '';
        $description = $config['description'] ?? '';
        $version = $config['version'] ?? '';
        $author = $config['author'] ?? '';
        $main = $config['main'] ?? '';

        $Extention = new Extention($name, $description, $version, $author, $main);
        $this->Extentions[] = $Extention;

        return $Extention;
    }

    public function scanExtentions($folder)
    {
        $ExtentionsDir = realpath($folder);
        if ($ExtentionsDir === false || !is_dir($ExtentionsDir)) {
            return;
        }

        $ExtentionDirs = glob($ExtentionsDir . '/*', GLOB_ONLYDIR);
        foreach ($ExtentionDirs as $ExtentionDir) {
            $this->loadExtention($ExtentionDir);
        }
    }

    public function startExtentions()
    {
        foreach ($this->Extentions as $Extention) {
            $Extention->start();
        }
    }
}
