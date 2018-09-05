<?php

namespace App\Repository;

use App\Entity\Fax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Fax|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fax|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fax[]    findAll()
 * @method Fax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaxRepository extends ServiceEntityRepository
{
    private const FAX_MAX_SHOW_PER_LIST = 20;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Fax::class);
    }

    /**
     * @return Fax[]
     */
    public function findFaxWithoutLocalFile(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.faxDirection = :incoming')
            ->andWhere('f.localFilePath IS NULL')
            ->setParameter('incoming', Fax::DIRECTION_INBOUND)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @return Fax[]
     */
    public function findFaxBeingSentOrReceived(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.faxStatus IS NULL OR f.faxStatus IN (:notFinishedStatus)')
            ->setParameter('notFinishedStatus', Fax::UNFINISHED_STATE_COLLECTION, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param null|string $onlyInboundOrOutbound
     * @param int         $maxCount
     *
     * @return Fax[]
     */
    public function findAllFax(
        ?string $onlyInboundOrOutbound = null,
        int $maxCount = self::FAX_MAX_SHOW_PER_LIST
    ): array {
        $qb = $this->createQueryBuilder('f')
            ->addOrderBy('f.updated', 'DESC')
            ->addOrderBy('f.created', 'DESC');

        $faxDirection = Fax::DIRECTION_INBOUND;
        if (Fax::DIRECTION_OUTBOUND === $onlyInboundOrOutbound) {
            $faxDirection = Fax::DIRECTION_OUTBOUND;
        }

        $qb->andWhere('f.faxDirection = :faxDirection')
            ->setParameter('faxDirection', $faxDirection)
            ->setFirstResult(0)
            ->setMaxResults($maxCount);

        return $qb->getQuery()
            ->execute();
    }

    /**
     * @param string $faxId
     *
     * @return null|Fax
     */
    public function findFaxByFaxId(string $faxId): ?Fax
    {
        return $this->findOneBy([
            'faxId' => $faxId,
        ]);
    }
}
