<?php

namespace Zank\Services;

use OSS\OssClient;

/**
 * Aliyun OSS .
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class AliyunOSS extends OssClient
{
    protected static $_wrapperClients = [];

    /**
     * Attempt to get the content-type of a file based on the extension.
     *
     * @param string $path
     *
     * @return string
     */
    public static function getMimeType($path)
    {
        $ext = substr(strrchr($path, '.'), 1);
        if (!$ext) {
            // shortcut
            return 'binary/octet-stream';
        }
        switch (strtolower($ext)) {
            case 'xls':
                $content_type = 'application/excel';
                break;
            case 'hqx':
                $content_type = 'application/macbinhex40';
                break;
            case 'doc':
            case 'dot':
            case 'wrd':
                $content_type = 'application/msword';
                break;
            case 'pdf':
                $content_type = 'application/pdf';
                break;
            case 'pgp':
                $content_type = 'application/pgp';
                break;
            case 'ps':
            case 'eps':
            case 'ai':
                $content_type = 'application/postscript';
                break;
            case 'ppt':
                $content_type = 'application/powerpoint';
                break;
            case 'rtf':
                $content_type = 'application/rtf';
                break;
            case 'tgz':
            case 'gtar':
                $content_type = 'application/x-gtar';
                break;
            case 'gz':
                $content_type = 'application/x-gzip';
                break;
            case 'php':
            case 'php3':
            case 'php4':
                $content_type = 'application/x-httpd-php';
                break;
            case 'js':
                $content_type = 'application/x-javascript';
                break;
            case 'ppd':
            case 'psd':
                $content_type = 'application/x-photoshop';
                break;
            case 'swf':
            case 'swc':
            case 'rf':
                $content_type = 'application/x-shockwave-flash';
                break;
            case 'tar':
                $content_type = 'application/x-tar';
                break;
            case 'zip':
                $content_type = 'application/zip';
                break;
            case 'mid':
            case 'midi':
            case 'kar':
                $content_type = 'audio/midi';
                break;
            case 'mp2':
            case 'mp3':
            case 'mpga':
                $content_type = 'audio/mpeg';
                break;
            case 'ra':
                $content_type = 'audio/x-realaudio';
                break;
            case 'wav':
                $content_type = 'audio/wav';
                break;
            case 'bmp':
                $content_type = 'image/bitmap';
                break;
            case 'gif':
                $content_type = 'image/gif';
                break;
            case 'iff':
                $content_type = 'image/iff';
                break;
            case 'jb2':
                $content_type = 'image/jb2';
                break;
            case 'jpg':
            case 'jpe':
            case 'jpeg':
                $content_type = 'image/jpeg';
                break;
            case 'jpx':
                $content_type = 'image/jpx';
                break;
            case 'png':
                $content_type = 'image/png';
                break;
            case 'tif':
            case 'tiff':
                $content_type = 'image/tiff';
                break;
            case 'wbmp':
                $content_type = 'image/vnd.wap.wbmp';
                break;
            case 'xbm':
                $content_type = 'image/xbm';
                break;
            case 'css':
                $content_type = 'text/css';
                break;
            case 'txt':
                $content_type = 'text/plain';
                break;
            case 'htm':
            case 'html':
                $content_type = 'text/html';
                break;
            case 'xml':
                $content_type = 'text/xml';
                break;
            case 'xsl':
                $content_type = 'text/xsl';
                break;
            case 'mpg':
            case 'mpe':
            case 'mpeg':
                $content_type = 'video/mpeg';
                break;
            case 'qt':
            case 'mov':
                $content_type = 'video/quicktime';
                break;
            case 'avi':
                $content_type = 'video/x-ms-video';
                break;
            case 'eml':
                $content_type = 'message/rfc822';
                break;
            default:
                $content_type = 'binary/octet-stream';
                break;
        }

        return $content_type;
    }

    /**
     * Register this object as stream wrapper client.
     *
     * @param string $name
     *
     * @return oss
     */
    public function registerAsClient($name)
    {
        self::$_wrapperClients[$name] = $this;

        return $this;
    }

    /**
     * Unregister this object as stream wrapper client.
     *
     * @param string $name
     *
     * @return oss
     */
    public function unregisterAsClient($name)
    {
        unset(self::$_wrapperClients[$name]);

        return $this;
    }

    /**
     * Get wrapper client for stream type.
     *
     * @param string $name
     *
     * @return oss
     */
    public static function getWrapperClient($name)
    {
        return self::$_wrapperClients[$name];
    }

    /**
     * Register this object as stream wrapper.
     *
     * @param string $name
     *
     * @return oss
     */
    public function registerStreamWrapper($name = 'oss')
    {
        stream_register_wrapper($name, \Zank\Streams\AliyunOssStream::class);
        $this->registerAsClient($name);
    }

    /**
     * Unregister this object as stream wrapper.
     *
     * @param string $name
     *
     * @return oss
     */
    public function unregisterStreamWrapper($name = 'oss')
    {
        stream_wrapper_unregister($name);
        $this->unregisterAsClient($name);
    }
} // END class AliyunOSS extends OssClient
