<?php

namespace Voyager\Admin\Classes;

use Illuminate\Support\Facades\File;
use ImageOptimizer;

class Image
{
    public function __construct($path)
    {
        if (!File::exists($path)) {
            throw new \Exception('File "'.$path.'" does not exist!');
        }

        $this->path = $path;
    }

    
    public function save($path = null)
    {

    }

    public function optimize($path = null)
    {
        ImageOptimizer::optimize($this->path, $path);
    }
}
