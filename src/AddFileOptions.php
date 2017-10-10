<?php

namespace Swaggest\CodeBuilder;

use Yaoi\BaseClass;

class AddFileOptions extends BaseClass
{
    public $skipIfExists = false;

    /** @var \Closure params: $content, $filePath, $srcPath; return $content|false */
    public $callback;

    /** @var \Closure params: $new, $old, returns $merged  */
    public $mergeCallback;
    
    public $deleteIfExists = false;

    public $skip = false;

    public $mustBeNotEmpty = false;

    /**
     * @param \Closure $callback
     * @return AddFileOptions
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param \Closure $mergeCallback
     * @return AddFileOptions
     */
    public function setMergeCallback($mergeCallback)
    {
        $this->mergeCallback = $mergeCallback;
        return $this;
    }

    /**
     * @param boolean $skipIfExists
     * @return AddFileOptions
     */
    public function setSkipIfExists($skipIfExists)
    {
        $this->skipIfExists = $skipIfExists;
        return $this;
    }

    /**
     * @param boolean $deleteIfExists
     * @return AddFileOptions
     */
    public function setDeleteIfExists($deleteIfExists)
    {
        $this->deleteIfExists = $deleteIfExists;
        return $this;
    }

    /**
     * @param boolean $mustBeNotEmpty
     * @return AddFileOptions
     */
    public function setMustBeNotEmpty($mustBeNotEmpty)
    {
        $this->mustBeNotEmpty = $mustBeNotEmpty;
        return $this;
    }

    /**
     * @param boolean $skip
     * @return AddFileOptions
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;
        return $this;
    }

}