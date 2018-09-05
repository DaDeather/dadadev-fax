<?php

namespace App\Command;

use App\Repository\FaxRepository;
use App\Service\FaxService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateSendingFaxStatusCommand extends Command
{
    protected static $defaultName = 'app:fax:update-status';

    /** @var FaxRepository */
    private $faxRepository;

    /** @var FaxService */
    private $faxService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param FaxRepository          $faxRepository
     * @param FaxService             $faxService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FaxRepository $faxRepository,
        FaxService $faxService,
        EntityManagerInterface $entityManager
    ) {
        $this->faxRepository = $faxRepository;
        $this->faxService = $faxService;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates fax status that are being sent,')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updatedFaxCount = 0;
        $failedUpdateFaxCount = 0;

        $io = new SymfonyStyle($input, $output);

        $faxInProgress = $this->faxRepository->findFaxBeingSentOrReceived();

        foreach ($faxInProgress as $fax) {
            $updatedFax = $this->faxService->updateAndGetFax($fax->getFaxId());

            if (null !== $updatedFax) {
                ++$updatedFaxCount;
                continue;
            }

            ++$failedUpdateFaxCount;
        }

        $io->success(
            sprintf(
                '%s fax were updated and %s failed to receive updates for.',
                $updatedFaxCount,
                $failedUpdateFaxCount
            )
        );
    }
}
