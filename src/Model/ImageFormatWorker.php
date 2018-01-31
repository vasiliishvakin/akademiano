<?php


namespace Akademiano\Content\Files\Images\Model;


use Akademiano\Content\Files\Model\File;
use Akademiano\Content\Files\Model\FileFormatCommand;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\Operator\Worker\Exception\TryNextException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\WorkersContainer;
use PHPixie\Image;

class ImageFormatWorker implements WorkerInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface
{
    const WORKER_ID = 'imageFormatWorker';
    const IMAGE_PROCESSOR_RESOURCE_ID = 'imageProcessor';

    use WorkerMappingTrait;


    /** @var Image */
    protected $imageProcessor;
    /** @var array */
    protected $templates = [];

    /** @var string */
    protected $publicDir;
    /** @var string */
    protected $dataDir;

    public static function getSelfInstance(WorkersContainer $container): WorkerInterface
    {
        $w = new static();
        $w->setImageProcessor($container->getDependencies()[self::IMAGE_PROCESSOR_RESOURCE_ID]);

        $config = $container->getDependencies()['config'];
        $templates = $config->get(['content', 'files', 'image', 'templates'], [])->toArray();
        $w->setTemplates($templates);
        return $w;
    }


    public static function getSupportedCommands(): array
    {
        return [
            FileFormatCommand::class,
            ImageFormatCommand::class,
        ];
    }

    public function execute(CommandInterface $command)
    {
        switch (true) {
            case $command instanceof FileFormatCommand:
                /** @var File $file */
                $file = $command->getFile();
                if (!$file->isImage()) {
                    throw new TryNextException(sprintf('Worker "%s" not work with not image files', self::class));
                }
            case $command instanceof ImageFormatCommand:
                if (!isset($file)) {
                    /** @var File $file */
                    $file = $command->getFile();
                }
                $template = $command->getTemplate();
                $extension = $command->getExtension();
                $savePath = $command->getSavePath();
                $isPublic = $command->isPublic();
                return $this->prepareFile($file, $savePath, $extension, $template, $isPublic);
            default:
                throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
    }

    /**
     * @return Image
     */
    public function getImageProcessor(): Image
    {
        return $this->imageProcessor;
    }

    /**
     * @param Image $imageProcessor
     */
    public function setImageProcessor(Image $imageProcessor): void
    {
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @return mixed
     */
    public function getTemplates(): array
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     */
    public function setTemplates(array $templates): void
    {
        $this->templates = $templates;
    }

    public function getTemplate($name)
    {
        return $this->templates[$name] ?? null;
    }

    /**
     * @return mixed
     */
    public function getPublicDir(): string
    {
        if (null === $this->publicDir) {
            if (defined('PUBLIC_DIR')) {
                $this->publicDir = PUBLIC_DIR;
            } else {
                throw new \LogicException('Public dir is not defined');
            }
        }
        return $this->publicDir;
    }

    /**
     * @param mixed $publicDir
     */
    public function setPublicDir(string $publicDir): void
    {
        $this->publicDir = $publicDir;
    }

    /**
     * @return string
     */
    public function getDataDir(): string
    {
        if (null === $this->dataDir) {
            if (defined('DATA_DIR')) {
                $this->dataDir = DATA_DIR;
            } else {
                throw new \LogicException('Data dir is not defined');
            }
        }
        return $this->dataDir;
    }

    /**
     * @param string $dataDir
     */
    public function setDataDir(string $dataDir): void
    {
        $this->dataDir = $dataDir;
    }

    public function prepareFile(File $file, string $savePath, string $extension, string $templateName, bool $isPublic = false)
    {
        if (empty($templateName)) {
            return $file;
        }

        $template = $this->getTemplate($templateName);
        if (null === $template) {
            throw new NotFoundException(sprintf('Not found config for image template "%s"', $templateName));
        }
        if (!is_callable($template)) {
            throw new \LogicException(sprintf('Image template #"%s" is not callable', $templateName));
        }

        $image = $this->getImageProcessor()->read($file->getFullPath());

        if ($extension === 'jpeg') {
            $format = 'jpg';
        } else {
            $format = $extension;
        }

        $dir = $savePath . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR . $file->getPosition();
        if (!file_exists($dir)) {
            $result = mkdir($dir, 0750, true);
            if (!$result) {
                throw new \RuntimeException(sprintf('Could not create directory %s', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new \RuntimeException(sprintf('Could not write file in directory %s', $dir));
        }

        $newPath = $dir . DIRECTORY_SEPARATOR . 'id' . $file->getId()->getHex() . '.' . $extension;

        $fullNewPath = ($isPublic ? $this->getPublicDir() : $this->getDataDir()) . DIRECTORY_SEPARATOR . $newPath;

        call_user_func($template, $image, $fullNewPath, $format);

        $newFile = clone $file;
        $newFile->setPath($newPath);
        return $newFile;
    }
}
