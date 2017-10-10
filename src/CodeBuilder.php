<?php

namespace Swaggest\CodeBuilder;

use Yaoi\Log;

abstract class CodeBuilder
{
    /**
     * @var StackTraceStorage
     */
    public $stack;

    public function __construct()
    {
        $this->stack = new StackTraceStorage();
        $this->log = Log::nil();
    }

    public function setBuilderVersion($builderVersion) {}

    public function rrmdir($dir, $includeExt = array(), $skipNames = array())
    {
        $isEmpty = true;

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (in_array($object, $skipNames)) {
                        $isEmpty = false;
                        continue;
                    }

                    if (is_dir($dir . "/" . $object)) {
                        $removed = $this->rrmdir($dir . "/" . $object, $includeExt);
                        if (!$removed) {
                            $isEmpty = false;
                        }
                    } else {
                        $ext = strtolower(pathinfo($object, PATHINFO_EXTENSION));

                        if ($includeExt && !isset($includeExt[$ext])) {
                            $isEmpty = false;
                            continue;
                        }
                        unlink($dir . "/" . $object);
                    }
                }
            }
            if ($isEmpty) {
                rmdir($dir);
            }
        }

        return $isEmpty;
    }

    public static function starMatch($pattern, $string)
    {
        $pattern = preg_quote($pattern, '/');
        $pattern = '/^(' . str_replace(array('%', '\\*', '\\?', '\\|'), array('.*', '.*', '.?', '|'), $pattern) . ')$/';

        return preg_match($pattern, $string);
    }

    public function recursiveFileList($dir, $includeExt = array(), $skipNames = array())
    {
        $result = array();

        $dir = realpath($dir);

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $skip = false;
                    foreach ($skipNames as $skipName) {
                        if (self::starMatch($skipName, $object)) {
                            $skip = true;
                            break;
                        }
                    }
                    if ($skip) {
                        continue;
                    }

                    if (is_dir($dir . "/" . $object)) {
                        $list = $this->recursiveFileList($dir . "/" . $object, $includeExt);
                        $result += $list;
                    } else {
                        $ext = strtolower(pathinfo($object, PATHINFO_EXTENSION));

                        if ($includeExt && !isset($includeExt[$ext])) {
                            continue;
                        }
                        $item = $dir . "/" . $object;
                        $result [strtolower($item)] = $item;
                    }
                }
            }
        }

        return $result;
    }

    public $files = array();
    /** @var AddFileOptions[] */
    public $options = array();

    public function addFile($filename, $content, AddFileOptions $options = null)
    {
        //var_dump($filename);
        $this->files[$filename] = $content;
        if (null !== $options) {
            $this->options[$filename] = $options;
        }
        return $this;
    }

    protected $originalFiles = array();
    public $ignoreFiles = array();

    protected function realSrcPath($srcPath)
    {
        if (!$srcPath) {
            $srcPath = '.';
        }
        $srcPath = rtrim($srcPath, '/');
        if (!file_exists($srcPath)) {
            mkdir($srcPath, 0755, true);
        }

        $srcPath = realpath($srcPath);
        return $srcPath;
    }

    protected function storeToDisk($srcPath)
    {
        $srcPath = $this->realSrcPath($srcPath);

        foreach ($this->files as $filename => $content) {
            $options = isset($this->options[$filename]) ? $this->options[$filename] : null;

            foreach ($this->ignoreFiles as $pattern) {
                if (self::starMatch($pattern, $filename)) {
                    $this->log->push("Skipping $filename write by $pattern ignore");
                    continue 2;
                }
            }

            $this->log->push("Storing $filename");
            $path = $srcPath . '/' . trim($filename, '/');
            $path = str_replace('/./', '/', $path);
            if ($rp = realpath($path)) {
                $this->log->push("Realpath $rp");
                $path = $rp;
            }

            $lPath = strtolower($path);
            if (isset($this->originalFiles[$lPath])) {
                unset($this->originalFiles[$lPath]);
            }

            $oldContent = null;

            if ($options) {
                $this->log->push("Options found");

                if ($options->callback) {
                    $content = $options->callback->__invoke($content, $path, $srcPath);
                    if ($content === false) {
                        continue;
                    }
                }

                if ($options->skip) {
                    if ($options->deleteIfExists && file_exists($path)) {
                        $this->log->push("Deleting existing by option");
                        unlink($path);
                    }
                    $this->log->push("Skipping by option");
                    continue;
                }

                if ($options->skipIfExists && file_exists($path)) {
                    $this->log->push("Skipping existing by option");
                    continue;
                }

                if ($options->deleteIfExists && file_exists($path)) {
                    $this->log->push("Deleting existing by option");
                    unlink($path);
                    continue;
                }

                if ($options->mergeCallback && file_exists($path)) {
                    $oldContent = file_get_contents($path);
                    $content = $options->mergeCallback->__invoke($content, $oldContent);
                }
            }

            if (null === $oldContent && file_exists($path)) {
                $oldContent = file_get_contents($path);
            }

            if ($oldContent === $content) {
                $this->log->push("Skipping unchanged");
                continue;
            }

            $dir = dirname($path);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->log->push("Saving to $path");
            file_put_contents($path, $content);
        }

        $cwd = getcwd();
        $cwdlen = strlen($cwd);
        foreach ($this->originalFiles as $originalFile) {
            if (substr($originalFile, 0, $cwdlen) === $cwd) {
                $originalFile = substr($originalFile, $cwdlen + 1);
            }
            foreach ($this->ignoreFiles as $pattern) {
                if (self::starMatch($pattern, $originalFile)) {
                    $this->log->push("Skipping $originalFile delete by $pattern ignore");
                    continue 2;
                }
            }

            $this->log->push("Removing redundant $originalFile");
            unlink($originalFile);
        }
    }


    public static function padLines($with, $text, $skipFirst = true)
    {
        $lines = explode("\n", $text);
        foreach ($lines as $index => $line) {
            if ($skipFirst && !$index) {
                continue;
            }
            $lines[$index] = $with . $line;
        }
        return implode("\n", $lines);
    }
}