<?php

namespace CodeRhapsodie\CrCoreExtraBundle\Command;


use Doctrine\DBAL\Connection;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class RemoveDirtyDraftCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var \Doctrine\DBAL\Driver\Connection
     */
    private $connection;

    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;

        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('code-rhapsodie:core:remove-dirty-draft')
            ->setDescription('')
            ->addOption(
                'contentIds',
                null,
                InputOption::VALUE_REQUIRED,
                'ContentIds separate with comma.',
                null
            );
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repository = $this->container->get('ezpublish.api.repository');
        $contentService = $repository->getContentService();

        $contentIds = $this->getDirtyObjectsId();

        foreach ($contentIds as $contentId) {

            $repository->sudo(
                function () use ($contentService, $contentId, $output) {
                    try{
                        $contentInfo = $contentService->loadContentInfo($contentId);

                        if($contentInfo->isDraft())
                        {
                            $contentService->deleteContent($contentInfo);
                            $output->writeln("<info>Content with id : '$contentId' was deleted.</info>");
                        }
                    } catch(NotFoundException $e){
                        $output->writeln('<error>Content with id : '.$contentId . ' not found.</error>');
                    }
                }
            );
        }

    }

    private function getDirtyObjectsId()
    {
        $query = $this->connection->createQueryBuilder()
            ->select('c.id')
            ->from('ezcontentobject', 'c')
            ->leftJoin('c', 'ezcontentobject_version', 'v', 'v.contentobject_id = c.id')
            ->where('c.status = 0 AND v.contentobject_id IS NULL');

        $stmt = $query->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
