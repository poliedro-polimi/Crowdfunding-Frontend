<?php
namespace nigiri\themes;

/**
 * Implements an empty theme with only the body output provided by the controller. Very useful for Ajax requests.
 * It is intended to be used with an explicit call to Site::switchTheme() in the controller that implements the ajax backend.
 * Specific output format can be implemented with a controller plugin like nigiri\plugin\JsonPlugin
 *
 * @package nigiri\themes
 */
class AjaxTheme implements ThemeInterface {
    private $body=null;

    public function append($str, $part = 'body')
    {
        $this->body = $str;
    }

    public function render()
    {
        echo $this->body;
    }
}
