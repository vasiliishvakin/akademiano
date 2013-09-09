<?php

namespace OrbisTools\Object;

class TmpFile
{
    protected  $fileName;

    public function __construct($path)
    {
        if (!is_writable($path)) {
            throw new \RuntimeException('Tmp directory is not writable');
        }
        do {
            $file = $path . "/" . mt_rand() . '.jpg';
            $fp = @fopen($file, 'x');
        } while (!$fp);

        fclose($fp);
        $this->fileName = $file;
    }

    public function __destruct()
    {
        $this->clear();
    }

    public function clear()
    {
        if (isset($this->fileName)) {
            if (file_exists($this->fileName)) {
                unlink($this->fileName);
            }
            unset ($this->fileName);
        }
    }

    public function __toString()
    {
        return $this->fileName;
    }

    public function __invoke()
    {
        return $this->fileName;
    }

}