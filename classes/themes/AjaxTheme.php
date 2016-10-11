<?php
namespace nigiri\themes;

/**
 * Implements rendering of data only in JSON format. Useful for Ajax Backends
 * It is intended to be used with an explicit call to Site::switchTheme() in the controller that implements the ajax backend
 *
 * @package nigiri\themes
 */
class AjaxTheme implements ThemeInterface {
    private $json=null;

    public function append($str, $part = 'body')
    {
        $this->json = json_encode($str);
    }

    public function render()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->json);
    }
}
