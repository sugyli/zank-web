<?php

use Symfony\Component\Yaml\Yaml;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        static $yaml;

        if (!is_array($yaml)) {
            $env = dirname(__DIR__).'/.env';

            if (file_exists($env)) {
                $env = file_get_contents($env);
            } else {
                $env = '';
            }

            $yaml = Yaml::parse($env);
        }

        if (!isset($yaml[$key])) {
            return $default;
        }

        return $yaml[$key];
    }
}
