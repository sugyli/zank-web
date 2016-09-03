<?php

use Symfony\Component\Yaml\Yaml;

if (!function_exists('env')) {
    /**
     * env YAML配置获取.
     *
     * @param string $key     键名
     * @param mixed  $default 默认值
     *
     * @return mixed
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
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

if (!function_exists('app')) {
    /**
     * get slim application.
     *
     * @return Slim\App
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function app()
    {
        return \Zank\App::getApplication();
    }
}

if (!function_exists('getAliyunOssBucket')) {
    /**
     * 获取oss的bucket名称.
     *
     * @return string
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function getAliyunOssBucket()
    {
        return app()->getContainer()->get('settings')->get('oss')['bucket'];
    }
}

if (!function_exists('attach_url')) {
    /**
     * 更具附件文件地址，获取URL.
     *
     * @param string $path 文件地址（相对）
     *
     * @return string URL
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function attach_url(string $path)
    {
        $settings = app()->getContainer()->get('settings')->get('oss');
        if ($settings['sign'] === true) {
            return app()
                ->getContainer()
                ->get('oss')
                ->signUrl(
                    getAliyunOssBucket(),
                    $path,
                    $settings['timeout']
                );
        }

        return sprintf('%s/%s', $settings['source_url'], $path);
    }
}
