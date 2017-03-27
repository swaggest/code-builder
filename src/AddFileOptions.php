<?php

namespace Swaggest\CodeBuilder;

use Yaoi\BaseClass;

class AddFileOptions extends BaseClass
{
    public $skipIfExists = false;

    /** @var \Closure params: $new, $old, returns $merged  */
    public $mergeCallback;
    
    public $deleteIfExists = false;

    public $skip = false;

    public $mustBeNotEmpty = false;

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




}