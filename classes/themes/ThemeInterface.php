<?php
namespace nigiri\theme;

interface ThemeInterface{
    public function append($str, $part='body');

    public function render();
}
