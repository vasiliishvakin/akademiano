<?php


namespace Akademiano\Config\Permanent;


use Akademiano\Config\Config;
use Akademiano\Config\FS\ConfigFile;
use Akademiano\Utils\Debug;
use Akademiano\Utils\Exception\CreateErrorException;
use Akademiano\Utils\Exception\NotAllowedPathException;
use Akademiano\Utils\Exception\NotWritableException;
use Akademiano\Utils\Exception\WriteErrorException;
use Akademiano\Utils\FileSystem;

class PermanentConfigFile extends PermanentConfig
{
    /** @var string */
    protected $file;

    /** @var string */
    protected $rootDir;

    public function __construct(Config $config, string $file, ?array $prefix)
    {
        parent::__construct($config, $prefix);
        $this->file = $file;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getRootDir(): string
    {
        if (null === $this->rootDir) {
            if (defined('ROOT_DIR')) {
                $this->rootDir = ROOT_DIR;
            } else {
                $root = realpath(dirname(__DIR__, 4));
                if (!$root) {
                    throw new \RuntimeException();
                }
                $this->rootDir = $root;
            }
        }
        return $this->rootDir;
    }

    public function getOld(): ?Config
    {
        $file = $this->getFile();
        if (!is_readable($file)) {
            return null;
        }
        return new Config((new ConfigFile($file))->getContent());
    }

    protected function prepare(): Config
    {
        $prefix = $this->getPrefix();
        $newConfig = new Config([], $this->getConfig()->getDiContainer());
        $currentConfig = $this->getConfig()->toArray();
        $prefix = $prefix ?? [];

        $newConfig->set($currentConfig, $prefix);

        $oldConfig = $this->getOld();
        if ($oldConfig) {
            $newConfig = $newConfig->joinLeft($oldConfig);
        }
        return $newConfig;
    }

    public function checkPath(?string $file = null): void
    {
        $file = $file ?? $this->getFile();
        if (!FileSystem::isFileInDir($this->getRootDir(), $file)) {
            throw new NotAllowedPathException($file, $this->getRootDir());
        }
        if (file_exists($file)) {
            if (!is_writable($file)) {
                throw new NotWritableException($file);
            }
        } else {
            $dir = dirname($file);
            if (file_exists($dir)) {
                if (!is_writable($dir)) {
                    throw new NotWritableException($dir);
                }
            } else {
                $result = mkdir($dir, 0750, true);
                if ($result) {
                    throw new CreateErrorException($dir);
                }
            }
        }
    }

    public function writeFile(string $content, ?string $file = null): void
    {
        $file = $file ?? $this->getFile();
        $this->checkPath($file);
        $content = "<?php\nreturn " . $content . ";\n";
        $openParams = 'c';
        $fn = fopen($file, $openParams);
        if (!$fn) {
            throw new WriteErrorException($file, $openParams);
        }
        if (!flock($fn, LOCK_EX)) {
            throw new WriteErrorException($file, 'LOCK_EX');
        }
        ftruncate($fn, 0);
        fwrite($fn, $content);
        fflush($fn);
        flock($fn, LOCK_UN);
        fclose($fn);
    }

    public function save(): void
    {
        if ($this->isSaved()) {
            return;
        }
        $config = $this->prepare();
        $content = Debug::var_export($config->toArray(), true);
        $this->writeFile($content);
        $this->isSaved = true;
    }
}
