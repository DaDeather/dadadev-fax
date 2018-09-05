<?php

namespace App\Tests\Functional\Repository;

use App\Repository\FaxRepository;
use App\Tests\Functional\AbstractControllerBaseWebTestCase;

class FaxRepositoryTest extends AbstractControllerBaseWebTestCase
{
    public function testFindFaxByFaxId(): void
    {
        $this->loadTestFixtures([
            __DIR__ . '/../Fixtures/Fax.yml',
        ]);

        self::bootKernel();
        $faxRepository = self::$container->get(FaxRepository::class);
        $faxById = $faxRepository->find(1);
        $faxByFaxId = $faxRepository->findFaxByFaxId($faxById->getFaxId());

        self::assertNotNull($faxById);
        self::assertNotNull($faxByFaxId);
        self::assertSame($faxById, $faxByFaxId);
    }
}
