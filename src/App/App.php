<?php

namespace Swaggest\CodeBuilder\App;


class App
{
    protected function putContents($path, $content) {
        if (file_exists($path)) {
            $original = file_get_contents($path);
            if ($original == $content) {
                return;
            }
        }
        file_put_contents($path, $content);

    }
}