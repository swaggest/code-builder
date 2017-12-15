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
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($path, $content);

    }
}