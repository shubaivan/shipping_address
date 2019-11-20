<?php

namespace App\Service;

use App\Exception\ValidatorException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectManager
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorageInterface;

    /**
     * Authenticator constructor.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validatorInterface
     * @param RequestStack $requestStack
     * @param TokenStorageInterface $tokenStorageInterface
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validatorInterface,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorageInterface
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validatorInterface;
        $this->requestStack = $requestStack;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    /**
     * @param string $class
     * @param null|string $requestType
     * @param array $groups
     * @param array $data
     *
     * @return array|\JMS\Serializer\scalar|mixed|object
     * @throws ValidatorException
     *
     */
    public function startProcessingEntity(
        string $class,
        ?string $requestType = 'request',
        array $groups = [],
        array $data = []
    )
    {
        $serializedData = $this->getRequestDataRepresent($class, $requestType, $groups, $data);

        return $this->handleEntity($serializedData, $class, $groups);
    }

    /**
     * @param string $class
     * @param null|string $requestType
     * @param array $groups
     * @param array $data
     * @return array|mixed|resource|string
     */
    public function getRequestDataRepresent(
        string $class,
        ?string $requestType = 'request',
        array $groups = [],
        array $data = []
    )
    {
        $paramRequest = $this->requestStack->getCurrentRequest();
        if (0 === strpos(
                $paramRequest
                    ->headers->get(
                        'content_type'
                    ),
                'application/json'
            ) && !$data) {
            $dataJson = $paramRequest->getContent();

            $serializedData = $dataJson;
        } elseif ($data) {
            $serializedData = $data;
        } else {
            $serializedData = $this->requestStack->getCurrentRequest()->$requestType->all();
        }

        if (is_array($serializedData)) {
            $serializedData = (array_filter($serializedData, function ($v, $k) {
                if ('null' === $v) {
                    return false;
                }

                return true;
            }, ARRAY_FILTER_USE_BOTH));
        }

        if (is_array($serializedData)) {
            $serializedData = $this->getSerializer()
                ->serialize($serializedData, 'json');
        }

        return $serializedData;
    }

    /**
     * @param $groups
     * @param $serializedData
     * @param $class
     *
     * @return array|\JMS\Serializer\scalar|mixed|object
     * @throws ValidatorException
     *
     */
    public function handleEntity($serializedData, $class, $groups = [])
    {
        if (is_array($serializedData)) {
            $serializedData = $this->getSerializer()
                ->serialize($serializedData, 'json');
        }
        $deserializationContext = null;
        $validateGroups = [];
        if ($groups) {
            $deserializationContext = DeserializationContext::create()->setGroups($groups);
            $validateGroups = $groups;
        }

        $dataValidate = $this->getSerializer()
            ->deserialize(
                $serializedData,
                $class,
                'json',
                $deserializationContext
            );

        $this->validateEntity($dataValidate, $validateGroups);

        return $dataValidate;
    }

    /**
     * @param object $entity
     * @param array $validateGroups
     *
     * @throws ValidatorException
     */
    public function validateEntity(
        $entity,
        array $validateGroups = []
    )
    {
        $validateGroups = $validateGroups ? $validateGroups : null;
        $errors = $this->getValidatorInterface()
            ->validate($entity, null, $validateGroups);
        if (count($errors)) {
            $validatorException = new ValidatorException($errors);

            throw $validatorException;
        }
    }

    /**
     * @param $entity
     * @param array $groups
     * @return mixed|string
     */
    public function serializerEntity($entity, $groups = [])
    {
        $serializationContext = null;
        if ($groups) {
            $serializationContext = SerializationContext::create()
                ->setGroups($groups);
        }

        return $this->getSerializer()
            ->serialize($entity, 'json', $serializationContext);
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return ValidatorInterface
     */
    private function getValidatorInterface()
    {
        return $this->validator;
    }
}
