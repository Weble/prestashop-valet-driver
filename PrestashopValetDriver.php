<?php

/**
 * Class PrestaShopValetDriver
 * @author Paolo Falomo @user https://gitlab.com/paolofalomo
 * @author Weble - Daniele Rosario @user https://github.com/Weble
 * @source https://github.com/Weble/prestashop-valet-driver
 * @version 2.0
 */
class PrestaShopValetDriver extends ValetDriver
{
    public static $ps_exclusions = ['ajax.php','dialog.php','ajax_products_list.php','autoupgrade/','filemanager/', 'webservice/'];
    /**
     * Determine if the driver serves the request.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        if(self::isPrestashop($sitePath) && self::stringContains($uri,self::$ps_exclusions)){
            return false;
        }elseif(self::isPrestashop($sitePath)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Determine if is prestashop
     * @param $sitePath
     * @return bool
     */
    public static function isPrestashop($sitePath){
        return file_exists($sitePath . '/classes/PrestashopAutoload.php');
    }

    /**
     * Check if string contains a string
     * @param $string
     * @param $doesContains
     * @return bool
     */
    public static function stringContains($string,$doesContains=null){
        if(is_array($doesContains)){
            foreach ($doesContains as $doesContain){
                if(self::stringContains($string,$doesContain)){
                    return true;
                }
            }
        }else{
            if (strpos($string, $doesContains) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {


        // from http://doc.prestashop.com/display/PS16/System+Administrator+Guide#SystemAdministratorGuide-NginxfriendlyURLs
        // rewrite ^/PRESTASHOP_FOLDER/api/?(.*)$ /PRESTASHOP_FOLDER/webservice/dispatcher.php?url=$1 last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$1$2.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$2/$1$2$3.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$2/$3/$1$2$3$4.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$2/$3/$4/$1$2$3$4$5.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$2/$3/$4/$5/$1$2$3$4$5$6.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$2/$3/$4/$5/$6/$1$2$3$4$5$6$7.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$2/$3/$4/$5/$6/$7/$1$2$3$4$5$6$7$8.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/p/$1/$2/$3/$4/$5/$6/$7/$8/$1$2$3$4$5$6$7$8$9.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/c/([0-9]+)(-[_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/c/$1$2.jpg last;
        // rewrite ^/PRESTASHOP_FOLDER/([0-9]+)(-[_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ /PRESTASHOP_FOLDER/img/c/$1$2.jpg last;



        // Basic static file
        if (is_file($staticFilePath = "{$sitePath}/{$uri}")) {

            return $staticFilePath;
        }


        // rewrite ^/([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$4/$1$2$3$4$5.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[4]}/{$matches[5]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}{$matches[6]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$4/$1$2$3$4$5.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {


            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[4]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$3/$1$2$3$4.jpg last;
        if (preg_match('/([0-9])([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[3]}/{$matches[1]}{$matches[2]}{$matches[3]}{$matches[4]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$2/$1$2$3.jpg last;
        if (preg_match('/([0-9])([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[2]}/{$matches[1]}{$matches[2]}{$matches[3]}.jpg";

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        // rewrite ^/([0-9])(-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ /img/p/$1/$1$2.jpg last;
        if (preg_match('/([0-9])(-[_a-zA-Z0-9-]*)\/(.*)/', $uri, $matches)) {
            $staticFilePath = "{$sitePath}/img/p/{$matches[1]}/{$matches[1]}{$matches[2]}.jpg";


            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

    

        // $fp = fopen('/tmp/data.txt', 'w');
        // fwrite($fp, "---> $uri \n");
        // fclose($fp);


        if (preg_match('/c\/([0-9]*)-category_default\/.*\.jpg/', $uri, $matches)) {

            $staticFilePath = "{$sitePath}/img/c/{$matches[1]}.jpg";


            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }

        }


        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {

        // rewrite ^/api/?(.*)$ /PRESTASHOP_FOLDER/webservice/dispatcher.php?url=$1 last;
        if (preg_match('/(api)\/(.*)/', $uri, $matches)) {
            $uri = '/webservice/dispatcher.php';
            $_GET['url'] = $matches[2];
            $_SERVER['SCRIPT_FILENAME'] = $sitePath.$uri;
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches);
                list($name, $password) = explode(':', base64_decode($matches[1]));
                $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
                $_GET['ws_key'] = $_SERVER['PHP_AUTH_USER'];
            }

            return $sitePath.$uri;
        }

        $parts = explode('/',$uri);
        if(!self::stringContains($uri,self::$ps_exclusions) && is_file($sitePath.$uri) && file_exists($sitePath.$uri)){
            $_SERVER['SCRIPT_FILENAME'] = $sitePath.$uri;
            return $sitePath.$uri;
        }
        if(isset($parts[1]) && $parts[1] !='' && file_exists($adminIdex = $sitePath . '/'. $parts[1] .'/index.php')){

            $_SERVER['SCRIPT_FILENAME'] = $adminIdex;
            $_SERVER['SCRIPT_NAME'] = '/'. $parts[1] .'/index.php';

            if(isset($_GET['controller']) || isset($_GET['tab'])){
                return $adminIdex;
            }
            return $adminIdex;
        }
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/index.php';
        return $sitePath . '/index.php';
    }
}
