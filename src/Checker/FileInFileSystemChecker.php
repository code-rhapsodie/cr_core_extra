<?php

declare(strict_types=1);

namespace CodeRhapsodie\CrCoreExtraBundle\Checker;

use eZ\Publish\Core\IO\Exception\BinaryFileNotFoundException;
use eZ\Publish\Core\IO\IOBinarydataHandler;

final class FileInFileSystemChecker implements CheckerInterface
{
    /**
     * @var IOBinarydataHandler
     */
    private $binarydataHandler;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string
     */
    private $projectDir;

    public function __construct(
        IOBinarydataHandler $binarydataHandler,
        string $prefix,
        string $projectDir
    ) {
        $this->binarydataHandler = $binarydataHandler;
        $this->prefix = $prefix;
        $this->projectDir = $projectDir;
    }

    public function check(array $files): array
    {
        $notfound = [];
        foreach ($files as $file) {
            $filename = $file['filename'] ?? $file['filepath'];
            if (0 === strpos($filename, $this->prefix)) {
                $filename = str_replace($this->prefix.'/', '', $filename);
            }

            try {
                $this->binarydataHandler->getContents($filename);
                continue;
            } catch (BinaryFileNotFoundException $e) {
            }

            $filename = 'original/application/'.$filename;

            try {
                $this->binarydataHandler->getContents($filename);
            } catch (BinaryFileNotFoundException $e) {
                $notfound[] = $file;
                continue;
            }
        }

        return $notfound;
    }
}
