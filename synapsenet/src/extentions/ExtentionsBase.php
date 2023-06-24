<?php 


namespace synapsenet\extentions;

use synapsenet\log\CoreLogger;

abstract class ExtentionBase
{
    protected $name;
    protected $description;
    protected $version;
    protected $author;
    protected $main;
    protected $coreLogger;

    public function __construct(string $extensionYmlPath, CoreLogger $coreLogger)
    {
        $this->loadExtensionYml($extensionYmlPath);
        $this->coreLogger = $coreLogger;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getMain(): string
    {
        return $this->main;
    }

    public function getLogger(): CoreLogger
    {
        return $this->coreLogger;
    }

    protected function loadExtensionYml(string $extensionYmlPath): void
    {
        $extensionConfig = yaml_parse_file($extensionYmlPath);

        $this->name = $extensionConfig['name'] ?? '';
        $this->description = $extensionConfig['description'] ?? '';
        $this->version = $extensionConfig['version'] ?? '';
        $this->author = $extensionConfig['author'] ?? '';
        $this->main = $extensionConfig['main'] ?? '';
    }

    public abstract function start(): void;
}
