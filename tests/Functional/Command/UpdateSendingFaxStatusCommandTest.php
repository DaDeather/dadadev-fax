<?php

namespace App\Tests\Functional\Command;

use App\Command\UpdateSendingFaxStatusCommand;
use App\Entity\Fax;
use App\Repository\FaxRepository;
use App\Service\FaxService;
use App\Tests\Functional\AbstractControllerBaseWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateSendingFaxStatusCommandTest extends AbstractControllerBaseWebTestCase
{
    /** @var FaxService|\PHPUnit_Framework_MockObject_MockObject */
    private $faxServiceMock;

    public function testExecuteWithoutEntities()
    {
        $command = $this->prepareCommandToTest();

        $this->loadTestFixtures();

        $commandTester = $this->executeCommand($command);

        $output = $commandTester->getDisplay();
        self::assertContains(' fax were updated and ', $output);
        self::assertContains(' failed to receive updates for.', $output);
    }

    public function testExecuteWithEntitiesSuccessfulUpdate()
    {
        $command = $this->prepareCommandToTest();

        $this->loadTestFixtures([
            __DIR__ . '/../Fixtures/FaxWithoutFinishedStatus.yml',
        ]);

        $fax = new Fax();
        $this->faxServiceMock->expects(self::exactly(20))
            ->method('updateAndGetFax')
            ->willReturn($fax);

        $commandTester = $this->executeCommand($command);

        $output = $commandTester->getDisplay();
        self::assertContains('20 fax were updated and 0 failed to receive updates for.', $output);
    }

    public function testExecuteWithEntitiesFailingUpdate()
    {
        $command = $this->prepareCommandToTest();

        $this->loadTestFixtures([
            __DIR__ . '/../Fixtures/FaxWithoutFinishedStatus.yml',
        ]);

        $commandTester = $this->executeCommand($command);

        $output = $commandTester->getDisplay();
        self::assertContains('0 fax were updated and 20 failed to receive updates for.', $output);
    }

    /**
     * @return Command
     */
    private function prepareCommandToTest(): Command
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        /* @var FaxService|\PHPUnit_Framework_MockObject_MockObject $guzzleClientMock */
        $this->faxServiceMock = $this->createMock(FaxService::class);

        $application->add(new UpdateSendingFaxStatusCommand(
            self::$container->get(FaxRepository::class),
            $this->faxServiceMock,
            self::$container->get(EntityManagerInterface::class)
        ));

        $command = $application->find('app:fax:update-status');

        return $command;
    }

    /**
     * @return CommandTester
     */
    private function executeCommand(Command $command): CommandTester
    {
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        return $commandTester;
    }
}
