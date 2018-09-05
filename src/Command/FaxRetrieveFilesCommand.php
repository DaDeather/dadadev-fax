<?php

namespace App\Command;

use App\Repository\FaxRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FaxRetrieveFilesCommand extends Command
{
    protected static $defaultName = 'app:fax:retrieve-files';

    /** @var FaxRepository */
    private $faxRepository;

    /** @var Client */
    private $guzzleClient;

    /** @var string */
    private $faxDocumentSavePath;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param FaxRepository          $faxRepository
     * @param Client                 $guzzleClient
     * @param EntityManagerInterface $entityManager
     * @param string                 $faxDocumentSavePath
     */
    public function __construct(
        FaxRepository $faxRepository,
        Client $guzzleClient,
        EntityManagerInterface $entityManager,
        string $faxDocumentSavePath
    ) {
        $this->faxRepository = $faxRepository;
        $this->guzzleClient = $guzzleClient;
        $this->faxDocumentSavePath = $faxDocumentSavePath;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Gets missing files for faxes in database,')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loadedMediaCount = 0;
        $failedMediaCount = 0;

        $io = new SymfonyStyle($input, $output);

        $faxWithoutLocalFile = $this->faxRepository->findFaxWithoutLocalFile();

        foreach ($faxWithoutLocalFile as $fax) {
            if (null === $fax->getMediaUrl()) {
                continue;
            }

            try {
                $fileName = $fax->getFaxId() . '.pdf';
                $response = $this->guzzleClient->request(
                    'GET',
                    $fax->getMediaUrl(),
                    [
                        'allow_redirects' => true,
                        'sink' => $this->faxDocumentSavePath . $fileName,
                    ]
                );

                $fileSizeInBytes = (int) $response->getHeader('Content-Length')[0] ?? 0;

                $fax->setLocalFileMime('application/pdf')
                    ->setLocalFilePath($fileName)
                    ->setLocalFileSizeInBytes($fileSizeInBytes);

                ++$loadedMediaCount;
            } catch (GuzzleException $guzzleException) {
                ++$failedMediaCount;
            }
        }

        $this->entityManager->flush();

        $io->success(
            sprintf(
                '%s missing fax files have been loaded while %s have been failed.',
                $loadedMediaCount,
                $failedMediaCount
            )
        );
    }
}
