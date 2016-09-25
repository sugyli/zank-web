<?php

namespace Zank\Util;

use Symfony\Component\Yaml\Yaml as BaseYaml;

class Yaml extends BaseYaml
{
    public $cfg = [];

    public static $clients = [];

    private function __construct(string $filename)
    {
        if (!file_exists($filename) || !is_file($filename)) {
            throw new \Exception(sprintf('file:%s not find.', $filename));
        }

        $content = file_get_contents($filename);
        $this->cfg = self::parse($content);
    }

    public function get($key, $default = null)
    {
        if (!isset($this->cfg[$key])) {
            return $default;
        }

        return $this->cfg[$key];
    }

    public static function addClient($key, string $filename)
    {
        if (!isset(self::$clients[$key])) {
            self::$clients[$key] = new self($filename);
        }
    }

    public static function getClient($key)
    {
        if (isset(self::$clients[$key])) {
            return self::$clients[$key];
        }
    }
}
