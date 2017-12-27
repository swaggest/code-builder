<?php

namespace Swaggest\CodeBuilder\App;


class App
{
    protected $storedFilesList = array();

    protected function getAbsoluteFilename($filename)
    {
        $path = [];
        foreach (explode('/', $filename) as $part) {
            // ignore parts that have no value
            if (empty($part) || $part === '.') continue;

            if ($part !== '..') {
                // cool, we found a new part
                array_push($path, $part);
            } else if (count($path) > 0) {
                // going back up? sure
                array_pop($path);
            } else {
                // now, here we don't like
                throw new \Exception('Climbing above the root is not permitted.');
            }
        }

        return '/' . join('/', $path);
    }

    protected function putContents($filepath, $content) {
        $filepath = $this->getAbsoluteFilename($filepath);
        $this->storedFilesList[$filepath] = $filepath;

        if (file_exists($filepath)) {
            $original = file_get_contents($filepath);
            if ($original == $content) {
                return;
            }
        }
        $dir = dirname($filepath);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($filepath, $content);
    }
}