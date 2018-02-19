<?php


namespace Akademiano\Attach\Model;


use Akademiano\Utils\ArrayTools;

class RequestFilesData
{
    const SEGREGATED_SECTION = '__segregated__';

    /** @var array */
    protected $rawData;

    /**
     * RequestFilesData constructor.
     * @param array $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getFileData(string $fileId): array
    {
        $rawData = $this->getRawData();

        $fileData = [];
        foreach ($rawData as $section => $sectionData) {
            if (is_array($sectionData)) {
                if ($section !== self::SEGREGATED_SECTION) {
                    if (isset($sectionData[$fileId])) {
                        $fileData[$section] = $sectionData[$fileId];
                    }
                } else {
                    //segregated
                    if (!is_array($rawData[$section])) {
                        continue;
                    }
                    foreach ($rawData[$section] as $segSection=>$segFileId) {
                        if ($segFileId === $fileId) {
                            $fileData[$segSection] = true;
                        }else {
                            $fileData[$segSection] = false;
                        }
                    }
                }
            }
        }
        $fileData = array_filter($fileData, function ($value) {
            return (is_string($value)) ? ($value === '' ? false : true) : true;
        });
        return $fileData;
    }
}
