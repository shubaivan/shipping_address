<?php

namespace App\Repository;

use App\Entity\ShippingAddress;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\RestBundle\Request\ParamFetcher;

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
     * @param User|object $user
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountDefaultByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.defaultAddress = :defaultAddress')
            ->andWhere('s.user = :user')
            ->setParameters([
                'defaultAddress' => 't',
                'user' => $user
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return ShippingAddress[]
     */
    public function getByDefault()
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.defaultAddress = :defaultAddress')
            ->setParameters([
                'defaultAddress' => 't'
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param User|object $user
     * @param bool $count
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getList(ParamFetcher $paramFetcher, User $user, $count = false)
    {

        $qb = $this->createQueryBuilder('s');

        $qb
            ->where('s.user = :user')
            ->setParameter('user', $user);
        if ($count) {
            $qb
                ->select('COUNT(s.id)');
            $query = $qb->getQuery();
            $result = $query->getSingleScalarResult();
        } else {
            $qb
                ->orderBy('s.' . $paramFetcher->get('sort_by'), $paramFetcher->get('sort_order'))
                ->setFirstResult($paramFetcher->get('count') * ($paramFetcher->get('page') - 1))
                ->setMaxResults($paramFetcher->get('count'))
                ->orderBy('s.createdAt', Criteria::DESC);
            $query = $qb->getQuery();
            $result = $query->getResult();
        }

        return $result;
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

    /**
     * @param $entity
     * @throws \Doctrine\ORM\ORMException
     */
    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
