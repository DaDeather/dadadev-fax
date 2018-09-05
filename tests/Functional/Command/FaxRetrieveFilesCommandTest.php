<?php

namespace App\Tests\Functional\Command;

use App\Command\FaxRetrieveFilesCommand;
use App\Repository\FaxRepository;
use App\Tests\Functional\AbstractControllerBaseWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class FaxRetrieveFilesCommandTest extends AbstractControllerBaseWebTestCase
{
    /** @var Client|\PHPUnit_Framework_MockObject_MockObject */
    private $guzzleClientMock;

    public function testExecuteWithoutEntities()
    {
        $command = $this->prepareCommandToTest();

        $this->loadTestFixtures();

        $commandTester = $this->executeCommand($command);

        $output = $commandTester->getDisplay();
        self::assertContains('missing fax files have been loaded while', $output);
        self::assertContains('have been failed', $output);
    }

    public function testExecuteWithEntity()
    {
        $command = $this->prepareCommandToTest();

        $this->loadTestFixtures([
            __DIR__ . '/../Fixtures/FaxWithoutLocalFile.yml',
        ]);

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $responseMock->expects(self::exactly(20))
            ->method('getHeader')
            ->with('Content-Length')
            ->willReturn([1337]);

        $this->guzzleClientMock->expects(self::exactly(20))
            ->method('request')
            ->willReturn($responseMock);

        $commandTester = $this->executeCommand($command);

        $output = $commandTester->getDisplay();
        self::assertContains('20 missing fax files have been loaded while 0 have been failed.', $output);
    }

    public function testExecuteWithEntityAndFailingLoad()
    {
        $command = $this->prepareCommandToTest();

        $this->loadTestFixtures([
            __DIR__ . '/../Fixtures/FaxWithoutLocalFile.yml',
        ]);

        $responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        $responseMock->expects(self::exactly(10))
            ->method('getHeader')
            ->with('Content-Length')
            ->willReturn([1337]);

        $this->guzzleClientMock->expects(self::exactly(20))
            ->method('request')
            ->willReturnCallback(function () use ($responseMock) {
                static $counter = 1;

                ++$counter;

                if (0 === $counter % 2) {
                    return $responseMock;
                }

                throw new ConnectException('Fail', new Request('GET', 'someUrl'));
            });

        $commandTester = $this->executeCommand($command);

        $output = $commandTester->getDisplay();
        self::assertContains('10 missing fax files have been loaded while 10 have been failed.', $output);
    }

    /**
     * @return Command
     */
    private function prepareCommandToTest(): Command
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        /* @var Client|\PHPUnit_Framework_MockObject_MockObject $guzzleClientMock */
        $this->guzzleClientMock = $this->createMock(Client::class);

        $application->add(new FaxRetrieveFilesCommand(
            self::$container->get(FaxRepository::class),
            $this->guzzleClientMock,
            self::$container->get(EntityManagerInterface::class),
            self::$container->getParameter('fax_document_path')
        ));

        $command = $application->find('app:fax:retrieve-files');

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
