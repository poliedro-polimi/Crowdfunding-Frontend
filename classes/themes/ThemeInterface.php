<?php
namespace nigiri\themes;

interface ThemeInterface{
    /**
     * Appends a string to a part of the final page to render
     * @param $str
     * @param string $part
     * @return mixed
     */
    public function append($str, $part='body');

    /**
     * Empties a par of the final page to render
     * @param $name
     * @return mixed
     */
    public function resetPart($name);

    /**
     * Renders the page
     * @return mixed
     */
    public function render();
}
