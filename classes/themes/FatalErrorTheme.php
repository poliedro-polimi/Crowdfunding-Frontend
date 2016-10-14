<?php
namespace nigiri\themes;

class FatalErrorTheme implements ThemeInterface{
    private $body;

    public function append($str, $part = 'body')
    {
        $this->body = $str;
    }

    public function render()
    {
        echo '
<!doctype html>
<html>
<head>
<style type="text/css">

</style>
<title>Errore Fatale</title>
</head>
<body>
'.$this->body.'
</body>
</html>';
    }
}
