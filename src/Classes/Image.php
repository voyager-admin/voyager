<?php

namespace Voyager\Admin\Classes;

use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as Intervention;

class Image
{
    private $path;
    private $intervention;
    private $extension;
    private $quality = 90;

    public function __construct($path)
    {
        if (!File::exists($path)) {
            throw new \Exception('File "'.$path.'" does not exist!');
        }

        $this->path = $path;
        $this->intervention = Intervention::make($path);
    }

    
    public function save($path = null)
    {
        if (is_null($this->extension)) {
            $this->intervention->save(($path ?? $this->path), $this->quality);
        } else {
            $this->intervention->save(($path ?? $this->path), $this->quality, $this->extension);
        }

        $this->intervention = $this->intervention->destroy();
    }

    public function blur(int $amount = 1)
    {
        $this->intervention = $this->intervention->blur($amount);

        return $this;
    }

    public function brightness(int $amount)
    {
        $this->intervention = $this->intervention->brightness($amount);

        return $this;
    }

    public function colorize(int $r, int $g, int $b)
    {
        $this->intervention = $this->intervention->colorize($r, $g, $b);

        return $this;
    }

    public function crop(int $width, int $height, int $x = null, int $y = null)
    {
        $this->intervention = $this->intervention->crop($width, $height, $x, $y);

        return $this;
    }

    public function fit($width, $height = null, $position = 'center', $upsize = false)
    {
        $this->intervention = $this->intervention->fit($width, $height, function ($constraint) use ($upsize) {
            if ($upsize === false) {
                $constraint->upsize();
            }
        }, $position);
        
        return $this;
    }

    public function flip(string $mode = 'h')
    {
        $this->intervention = $this->intervention->flip($width, $height, $x, $y);

        return $this;
    }

    public function resize($width, $height, $aspect = true, $upsize = false) {
        $this->intervention = $this->intervention->resize($width, $height, function ($constraint) use ($aspect, $upsize) {
            if ($aspect === true) {
                $constraint->aspectRatio();
            }
            if ($upsize === false) {
                $constraint->upsize();
            }
        });
        
        return $this;
    }

    public function __destruct()
    {
        if ($this->intervention instanceof Intervention) {
            $this->intervention = $this->intervention->destroy();
        }
    }
}
