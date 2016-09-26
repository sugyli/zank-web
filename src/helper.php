<?php

use Zank\Util\Yaml;

if (!function_exists('cfg')) {
    /**
     * cfg YAML配置获取.
     *
     * @param string $key     键名
     * @param mixed  $default 默认值
     *
     * @return mixed
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function cfg(string $key, $default = null)
    {
        $clientKey = '.zank.yaml';
        $filename = dirname(__DIR__).'/.zank.yaml';

        Yaml::addClient($clientKey, $filename);

        return Yaml::getClient($clientKey)->get($key, $default);
    }
}

if (!function_exists('get_oss_bucket_name')) {
    /**
     * 获取oss的bucket名称.
     *
     * @return string
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function get_oss_bucket_name()
    {
        return \Zank\Application::getContainer()->get('settings')->get('oss')['bucket'];
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
        $settings = \Zank\Application::getContainer()->get('settings')->get('oss');

        if ($settings['sign'] === true) {
            return \Zank\Application::getContainer()
                ->get('oss')
                ->signUrl($settings['bucket'], $path, $settings['timeout']);
        }

        return sprintf('%s/%s', $settings['source_url'], $path);
    }
}

if (!function_exists('database_source_dir')) {
    /**
     * 获取数据相关资源目录.
     *
     * @return string
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function database_source_dir()
    {
        return dirname(__DIR__).'/database';
    }
}
