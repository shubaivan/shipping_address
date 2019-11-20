<?php

namespace App\Repository;

use App\Entity\ShippingAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ShippingAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingAddress[]    findAll()
 * @method ShippingAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingAddress::class);
    }

    /**
     * @param ShippingAddress $shippingAddress
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(ShippingAddress $shippingAddress)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($shippingAddress);
        $entityManager->flush();
    }
}
