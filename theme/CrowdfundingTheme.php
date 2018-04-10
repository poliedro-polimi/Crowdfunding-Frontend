<?php

namespace site\theme;

use nigiri\Controller;
use nigiri\exceptions\FileNotFound;
use nigiri\Site;
use nigiri\themes\Theme;
use nigiri\views\Html;
use nigiri\views\Url;

class CrowdfundingTheme extends Theme {
    public function render()
    {
        $this->title .= (empty($this->title)?'':' - ').Site::getParam('site_name');

        if(empty($this->banner_img)){
            $this->banner_img = Url::resource("assets/imgs/banner1.png");
        }

        $ready = '';
        if(!empty($this->script_on_ready)){
            $ready = <<<READY
<script type="application/javascript">
$(function(){
    {$this->script_on_ready}
});
</script>
READY;
        }

        echo Controller::renderView('site/layout', [
            'title' => $this->title,
            'head' => $this->head,
            'banner_img' => $this->banner_img,
            'body' => $this->body,
            'script' => $this->script,
            'ready' => $ready
        ]);
    }
}
