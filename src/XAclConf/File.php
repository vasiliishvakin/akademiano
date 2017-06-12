<?php

namespace Akademiano\Acl\XAclConf;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;

class File implements ArrayableInterface
{
    protected $file;

    protected $rawData;

    /** @var Section[] */
    protected $sections;

    /**
     * @param string $file
     */
    public function __construct($file = null)
    {
        if (null !== $file) {
            $this->setFile($file);
        }
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    protected function readRaw()
    {
        $file = $this->getFile();
        if (null === $file) {
            return[];
        }
        if (!is_readable($file)) {
            throw new \RuntimeException("File {$file} not readable");
        }
        return file($file,  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    public function getRaw()
    {
        if (null === $this->rawData) {
            $this->rawData = $this->readRaw();
        }
        return $this->rawData;
    }

    public function isSection($string)
    {
        $first = mb_substr($string, 0, 1);
        $last = mb_substr($string, -1, 1);
        return ($first === "[" && $last === "]");
    }

    public function addSection(Section $section)
    {
        $this->sections[$section->getName()] = $section;
    }

    public function getSections()
    {
        if (null === $this->sections) {
            $rawData = $this->getRaw();
            $sections = [];
            $section = new Section();
            foreach ($rawData as $row) {
                $row = trim($row);
                if ($this->isSection($row)) {
                    if ($section instanceof Section) {
                        $sections[$section->getName()] = $section;
                    }
                    $sectionName = mb_substr($row, 1, mb_strlen($row) - 2);
                    $section = new Section($sectionName);
                } else {
                    $itemData = explode("=", $row);
                    $item = new Item(trim($itemData[0]));
                    if (isset($itemData[1])) {
                        $item->setAccess($itemData[1]);
                    }
                    $section->addItem($item);
                }
            }
            $sections[$section->getName()] = $section;
            $this->sections = $sections;
        }
        return $this->sections;
    }

    public function toArray()
    {
        $data = [];
        foreach ($this->getSections() as $section) {
            $data[$section->getName()] = $section->toArray();
        }
        return $data;
    }

    public function merge(File $file)
    {
        foreach ($file->getSections() as $section) {
            $this->addSection($section);
        }
    }

    public function __toString()
    {
        $strSection = [];
        foreach ($this->getSections() as $section) {
            $strSection[] = (string) $section;
        }
        return implode(PHP_EOL, $strSection);
    }

    public function optimize()
    {
        $sections = $this->getSections();
        $sections = array_filter($sections, function (Section $section) {
            return (!empty($section->getName()));
        });
        $this->sections = $sections;
    }
}
