<?php
namespace nigiri\themes;

interface ThemeInterface{
    public function append($str, $part='body');

    public function render();
}
