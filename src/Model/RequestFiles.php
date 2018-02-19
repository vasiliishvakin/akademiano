<?php


namespace Akademiano\Attach\Model;


use Akademiano\Entity\Uuid;
use Akademiano\HttpWarp\Request;
use Akademiano\Utils\FileSystem;
use Akademiano\Utils\Object\Collection;

class RequestFiles
{
    const REQUEST_PARAM_UPLOADED = 'files';
    const REQUEST_PARAM_UPDATED = 'filesUpdate';
    const REQUEST_PARAM_DATA = 'filesData';
    const REQUEST_PARAM_DELETE = 'filesDelete';

    /** @var Request */
    protected $request;

    protected $requestParamUploadedName = self::REQUEST_PARAM_UPLOADED;
    protected $requestParamUpdatedName = self::REQUEST_PARAM_UPDATED;
    protected $requestParamDataName = self::REQUEST_PARAM_DATA;
    protected $requestParamDeletedName = self::REQUEST_PARAM_DELETE;

    protected $filesTypes = [
        FileSystem::FST_IMAGE,
    ];

    /** @var int|null */
    protected $maxFilesSize;

    /** @var RequestFilesData */
    protected $filesData;


    /** @var  array */
    protected $deleted;
    /** @var  Collection|RequestFileInfo[] */
    protected $updated;
    /** @var  Collection|RequestFileInfo[] */
    protected $uploaded;


    /**
     * RequestFiles constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getRequestParamUploadedName(): string
    {
        return $this->requestParamUploadedName;
    }

    /**
     * @param string $requestParamUploadedName
     */
    public function setRequestParamUploadedName(string $requestParamUploadedName): self
    {
        $this->requestParamUploadedName = $requestParamUploadedName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestParamDataName(): string
    {
        return $this->requestParamDataName;
    }

    /**
     * @param string $requestParamDataName
     */
    public function setRequestParamDataName(string $requestParamDataName): self
    {
        $this->requestParamDataName = $requestParamDataName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestParamUpdatedName(): string
    {
        return $this->requestParamUpdatedName;
    }

    /**
     * @return string
     */
    public function getRequestParamDeletedName(): string
    {
        return $this->requestParamDeletedName;
        return $this;
    }

    /**
     * @return array
     */
    public function getFilesTypes(): array
    {
        return $this->filesTypes;
    }

    /**
     * @return int|null
     */
    public function getMaxFilesSize(): ?int
    {
        return $this->maxFilesSize;
    }

    /**
     * @param int|null $maxFilesSize
     */
    public function setMaxFilesSize(?int $maxFilesSize): self
    {
        $this->maxFilesSize = $maxFilesSize;
        return $this;
    }

    /**
     * @param array $filesTypes
     */
    public function setFilesTypes(array $filesTypes): self
    {
        $this->filesTypes = $filesTypes;
        return $this;
    }

    /**
     * @return RequestFilesData
     */
    public function getFilesData(): RequestFilesData
    {
        if (null === $this->filesData) {
            $request = $this->getRequest();
            $paramDataName = $this->getRequestParamDataName();
            $this->filesData = new RequestFilesData($request->getParam($paramDataName), []);
        }
        return $this->filesData;
    }

    /**
     * @return UploadFileInfo[]|Collection
     */
    public function getUploaded(): Collection
    {
        if (null === $this->uploaded) {
            $request = $this->getRequest();
            $rawUploaded = [];
            foreach ($this->getFilesTypes() as $type) {
                $rawUploadedByType = $request->getFiles(
                    $this->getRequestParamUploadedName(),
                    $type,
                    $this->getMaxFilesSize()
                );
                $rawUploaded = $rawUploaded + $rawUploadedByType;
            }
            if (empty($rawUploaded)) {
                $this->uploaded = new Collection();
            } else {
                $files = [];
                $filesData = $this->getFilesData();
                foreach ($rawUploaded as $uploadedFile) {
                    $id = str_replace(".", "_", $uploadedFile->getName());
                    $fileData = $filesData->getFileData($id);
                    $fileInfo = new UploadFileInfo($fileData);
                    $fileInfo->setUploadedFile($uploadedFile);
                    $files[$id] = $fileInfo;
                }
                $this->uploaded = new  Collection($files);
            }
        }
        return $this->uploaded;
    }

    /**
     * @return array
     */
    public function getDeleted(): array
    {
        if (null === $this->deleted) {
            $deleted = $this->getRequest()->getParam($this->getRequestParamDeletedName(), []);
            $deleted = array_map(function ($value) {
                return Uuid::normalize($value);
            }, $deleted);
            $this->deleted = $deleted;
        }
        return $this->deleted;
    }

    /**
     * @return RequestFileInfo[]|Collection
     */
    public function getUpdated(): Collection
    {
        if (null === $this->updated) {
            $request = $this->request;
            $updatedIds = $request->getParam($this->getRequestParamUpdatedName());
            if (empty($updatedIds)) {
                $this->updated = new Collection();
            } else {
                $filesData = $this->getFilesData();
                $files = [];
                foreach ($updatedIds as $id) {
                    $idNormal = Uuid::normalize($id);
                    $fileData = $filesData->getFileData($id);
                    $files[$idNormal] = $fileData;
                }
                $deletedIds = $this->getDeleted();
                $files = array_diff_key($files, array_flip($deletedIds));
                $this->updated = new Collection($files);
            }
        }
        return $this->updated;
    }
}
