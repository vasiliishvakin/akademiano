<?php

namespace DeltaUtils;

use DeltaUtils\Object\TmpFile;

class FileConverter
{
    protected $tmpDir;
    protected $pInput = '#INPUT#';
    protected $pOutput = '#OUTPUT#';

    /**
     * @param mixed $tmpDir
     */
    public function setTmpDir($tmpDir)
    {
        $this->tmpDir = $tmpDir;
    }

    /**
     * @return mixed
     */
    public function getTmpDir()
    {
        if (!$this->tmpDir) {
            $this->tmpDir = sys_get_temp_dir();
        }

        return $this->tmpDir;
    }

    /**
     * @param mixed $pInput
     */
    public function setPInput($pInput)
    {
        $this->pInput = $pInput;
    }

    /**
     * @return mixed
     */
    public function getPInput()
    {
        return $this->pInput;
    }

    /**
     * @param mixed $pOutput
     */
    public function setPOutput($pOutput)
    {
        $this->pOutput = $pOutput;
    }

    /**
     * @return mixed
     */
    public function getPOutput()
    {
        return $this->pOutput;
    }

    public function convert($inputFile, $options, $outputFile = null)
    {
        $tmpFile = new TmpFile($this->getTmpDir());
        if (is_array($options)) {
            if (!isset($options['command'])) {
                throw new \InvalidArgumentException('specify command in options');
            }
            $command = $options['command'];
            $pInput = isset($options['pInput']) ? $options['pInput'] : $this->getPInput();
            $pOutput = isset($options['pOutput']) ? $options['pOutput'] : $this->pOutput;
        } else {
            $command = $options;
            $pInput = $this->getPInput();
            $pOutput = $this->pOutput;
        }
        if (empty($outputFile)) {
            $execCmd = str_replace($pInput, escapeshellarg($inputFile), $command);
        } else {
            $execCmd = str_replace([$pInput, $pOutput], [escapeshellarg($inputFile), $tmpFile], $command);
        }
        $outputData = [];
        exec($execCmd, $outputData, $result);
        $result = ($result === 0) ? true : false;
        if (!$result) {
            throw new Exception("Error in executing command $execCmd");
        }
        if (!empty($outputFile)) {
            if (filesize($tmpFile) == 0) {
                unset($tmpFile);
                throw new Exception("Error in creating file from $inputFile to $outputFile");
            }
            $result = rename($tmpFile, $outputFile);
            if (!$result) {
                throw new Exception("Error in creating file $outputFile");
            }
        }

        return $result;
    }


    public function exec($inputFile, $options, $outputFile = null)
    {
        if (is_array($options)) {
            if (!isset($options['command'])) {
                throw new \InvalidArgumentException('specify command in options');
            }
            $command = $options['command'];
            $pInput = isset($options['pInput']) ? $options['pInput'] : $this->getPInput();
            $pOutput = isset($options['pOutput']) ? $options['pOutput'] : $this->pOutput;
        } else {
            $command = $options;
            $pInput = $this->getPInput();
            $pOutput = $this->pOutput;
        }
        if (empty($outputFile)) {
            $execCmd = str_replace($pInput, escapeshellarg($inputFile), $command);
        } else {
            $execCmd = str_replace([$pInput, $pOutput], [escapeshellarg($inputFile), escapeshellarg($outputFile)],
                $command);
        }
        $outputData = [];
        exec($execCmd, $outputData, $result);
        $result = ($result === 0) ? true : false;
        if (!$result) {
            throw new Exception("Error in executing command $execCmd");
        }

        return $result;
    }

}
