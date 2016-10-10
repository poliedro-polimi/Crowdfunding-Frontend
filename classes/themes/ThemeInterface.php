<?php
namespace site\theme;

interface ThemeInterface{
    public function append($str, $part='body');

    public function render();
}
