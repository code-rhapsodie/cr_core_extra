<?php

declare(strict_types=1);

namespace CodeRhapsodie\CrCoreExtraBundle\Command;

use CodeRhapsodie\CrCoreExtraBundle\Checker\FileInFileSystemChecker;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckEzFileCommand extends Command
{
    protected static $defaultName = 'code-rhapsodie:core:check-ez-file';
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var FileInFileSystemChecker
     */
    private $fileInFileSystemChecker;

    public function __construct(Connection $connection, FileInFileSystemChecker $fileInFileSystemChecker)
    {
        parent::__construct(null);
        $this->connection = $connection;
        $this->fileInFileSystemChecker = $fileInFileSystemChecker;
    }

    protected function configure()
    {
        $this->setHelp('Check if file found in database exists on filesystem.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stmt = $this->connection->prepare('select * from ezbinaryfile');
        $stmt->execute();

        while (false !== ($file = $stmt->fetch(\PDO::FETCH_ASSOC))) {
            $notFound = $this->fileInFileSystemChecker->check([$file]);
            if (0 < count($notFound)) {
                $output->writeln(sprintf('File not found : <comment>%s</comment> Attribute id : <info>%s</info> in version <info>%s</info>',
                    $file['filename'], $file['contentobject_attribute_id'], $file['version']));
            }
        }

        $stmt = $this->connection->prepare('select * from ezimagefile');
        $stmt->execute();

        while (false !== ($file = $stmt->fetch(\PDO::FETCH_ASSOC))) {
            $notFound = $this->fileInFileSystemChecker->check([$file]);
            if (0 < count($notFound)) {
                $output->writeln(sprintf('Image not found : <comment>%s</comment> Attribute id : <info>%s</info>',
                    $file['filepath'], $file['contentobject_attribute_id']));
            }
        }
    }
}
