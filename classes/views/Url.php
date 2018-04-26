<?php
namespace nigiri\views;
use nigiri\Site;

/**
 * Utility class that helps generating correct URLs for pages and resources of the site
 */
class Url
{
    /**
     * Creates a URL to a page of the website.
     * Takes into account usage of CLEAN_URLS and URL_PREFIX
     * @param string $l_page : the name of the page
     * @param string $query : the GET query. Can be a string or an array of key value pairs
     * @param bool $absolute : generates an absolute URL instead of one relative to the document root
     * @return string
     */
    public static function to($l_page = '', $query = '', $absolute = false, $language = '')
    {
        if (empty($l_page)) {
            $url = Site::getRouter()->getPage();

            if(!empty($language)) {
                $boom = explode('/', $url);
                $lang = Site::getParam("languages", []);
                if (in_array($boom[0], $lang)) {//If the first argument is a language code
                    array_shift($boom);
                    $url = $language.'/'.implode('/', $boom);
                }
                else{
                    $url = $language.'/'.implode('/', $boom);
                }
            }
        } else {
            $url = $l_page;
            $language = empty($language) ? Site::getRouter()->getRequestedLanguage() : $language;

            if($url!='/') {
                $boom = explode('/', $url);
                $lang = Site::getParam("languages", []);
                if (in_array($boom[0], $lang)) {
                    if (!empty($language)) {
                        array_shift($boom);
                        $url = $language . '/' . implode('/', $boom);
                    }
                } else {
                    $url = $language . '/' . implode('/', $boom);
                }
            }
            else{
                $url = $language . '/';
            }
        }

        if (!Site::getParam('clean_urls')) {
            $url = 'index.php?show_page=' . $url;
        }

        return self::make($url, $query, $absolute);
    }

    /**
     * Low level function to make urls of this website
     * @param string $path : actual path from the index.php page to the resource
     * @param string $query : the GET query. Can be a string or an array of key value pairs
     * @param bool $absolute : generates an absolute URL instead of one relative to the document root
     * @return string the requested URL
     */
    private static function make($path, $query = '', $absolute = false)
    {
        $url = $path;

        if (!empty($query)) {
            if (is_array($query)) {
                $temp = array();
                foreach ($query as $k => $v) {
                    $temp[] = $k . '=' . urlencode($v);
                }
                $query = implode('&', $temp);
            }

            if (strpos($url, '?') === false) {
                $url .= '?' . $query;
            } else {
                $url .= '&' . $query;
            }
        }

        if ($url[0] == '/') {
            $url = substr($url, 1);
        }

        if (Site::getParam('url_prefix') != '') {
            $pre = Site::getParam('url_prefix');
            if($pre[0]!='/'){
                $pre = '/'.$pre;
            }
            $url = $pre . $url;
        } else {
            $url = '/' . $url;
        }

        if ($absolute) {
            $url = (empty($_SERVER["REQUEST_SCHEME"]) ? 'http' : $_SERVER["REQUEST_SCHEME"]) . '://' . $_SERVER["HTTP_HOST"] . $url;
        }

        return $url;
    }

    /**
     * Function to make urls of resources in the website (e.g. images, css, js)
     * @param string $path
     * @param bool $absolute
     * @param string $query
     * @return string the URL to the resource
     */
    public static function resource($path, $absolute = false, $query = '')
    {
        return self::make($path, $query, $absolute);
    }
}
