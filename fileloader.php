<?php

interface FileLoader
{
    public function loadFile(string $path): array;
}

class JsonFileLoader implements FileLoader
{
    public function loadFile(string $path): array
    {
        $contents = file_get_contents($path);
        return json_decode($contents, true);
    }
}

class IniFileLoader implements FileLoader
{
    public function loadFile(string $path): array
    {
        return parse_ini_file($path, true);
    }
}

class CachedFileLoader implements FileLoader
{
    private $cache = [];
    /**
     * @var FileLoader
     */
    private $realLoader;

    public function __construct(FileLoader $realLoader)
    {
        $this->realLoader = $realLoader;
    }

    public function loadFile(string $path): array
    {
        if (isset($this->cache[$path])) {
            return $this->cache[$path];
        }

        $this->cache[$path] = $this->realLoader->loadFile($path);
        return $this->cache[$path];
    }
}

function doSomething(FileLoader $loader, string $path)
{
    $parameters = $loader->loadFile($path);
    var_dump($parameters);
}

$loader = new CachedFileLoader(
    new JsonFileLoader()
);

doSomething($loader, 'foo.json');

$anotherLoader = new CachedFileLoader(
    new IniFileLoader()
);

doSomething($loader, 'foo.ini');
