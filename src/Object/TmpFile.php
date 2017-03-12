<?php

namespace Akademiano\Utils\Object;

class TmpFile
{
    protected  $fileName;

    public function __construct($path = null, $ext = null)
    {
        if (is_null($path)) {
            $path = sys_get_temp_dir();
        }
        if (is_null($ext)) {
            $ext = '.tmp';
        }

        if (!is_writable($path)) {
            throw new \RuntimeException('Tmp directory is not writable');
        }
        do {
            $file = $path . "/" . mt_rand() . $ext;
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
