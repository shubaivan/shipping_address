<?php

namespace App\Service;

use App\Entity\ShippingAddress;
use App\Exception\ValidatorException;
use App\Repository\ShippingAddressRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ShippingAddressObjectManager extends ObjectManager
{
    /**
     * @var ShippingAddressRepository
     */
    private $shippingAddressRepository;

    /**
     * ShippingAddressObjectManager constructor.
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validatorInterface,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorageInterface,
        ShippingAddressRepository $shippingAddressRepository
    )
    {
        parent::__construct($serializer, $validatorInterface, $requestStack, $tokenStorageInterface);
        $this->shippingAddressRepository = $shippingAddressRepository;
    }

    /**
     * @return ShippingAddress
     * @throws ValidatorException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createEntity()
    {
        /** @var ShippingAddress $model */
        $model = $this->startProcessingEntity(
            ShippingAddress::class,
            'request',
            [ShippingAddress::GROUP_POST]
        );
        $defaultShippingAddress = $this->getShippingAddressRepository()
            ->getCountDefaultByUser($this->tokenStorageInterface->getToken()->getUser());
        if (!$defaultShippingAddress) {
            $model->setDefaultAddress(true);
        }
        $this->getShippingAddressRepository()->save($model);

        return $model;
    }

    /**
     * @return ShippingAddress
     * @throws ValidatorException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateEntity()
    {
        $defaultShippingAddresses = $this->getShippingAddressRepository()
            ->getByDefault();
        if (count($defaultShippingAddresses)) {
            foreach ($defaultShippingAddresses as $address) {
                $address->setDefaultAddress(false);
            }
            $this->getShippingAddressRepository()->persist($address);
        }

        $this->getShippingAddressRepository()->flush();

        /** @var ShippingAddress $model */
        $model = $this->startProcessingEntity(
            ShippingAddress::class,
            'request',
            [ShippingAddress::GROUP_PUT]
        );

        $this->getShippingAddressRepository()->flush();

        return $model;
    }

    /**
     * @return ShippingAddressRepository
     */
    public function getShippingAddressRepository(): ShippingAddressRepository
    {
        return $this->shippingAddressRepository;
    }
}
