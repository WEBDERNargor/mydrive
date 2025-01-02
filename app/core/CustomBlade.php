<?php

namespace App\core;

use Jenssegers\Blade\Blade;

class CustomBlade extends Blade
{
    protected $defaultScript;

    public function __construct($views, $cache, $defaultScript)
    {
        parent::__construct($views, $cache);
        $this->defaultScript = $defaultScript;
    }

    public function render($view, array $data = [], $mergeData = []): string
    {
        // เรียกใช้ parent render
        $output = parent::render($view, $data, $mergeData);
        $edit1 = str_replace('<body>', "
        <script>

        


function setCookie(name, value, days) {
    var expires = \"\";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = \"; expires=\" + date.toUTCString();
    }
    document.cookie = name + \"=\" + (value || \"\") + expires + \"; path=/\";
}

function getCookie(name) {
    var nameEQ = name + \"=\";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name + '=; Max-Age=-99999999;';
}



        </script>
        ", $output);
        $edit2 = str_replace('</body>', $this->defaultScript . '</body>', $edit1);
        // แทรก default script หลังจาก </body>
        return $edit2;
    }
}
