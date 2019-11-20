<?php

namespace App\Controller\Api\ShippingAddress;

use App\Controller\Api\AbstractRestController;
use App\Entity\ShippingAddress;
use App\Exception\ValidatorException;
use App\Repository\ShippingAddressRepository;
use App\Service\ObjectManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShippingAddressApiController extends AbstractRestController
{
    /**
     * @var ShippingAddressRepository
     */
    private $shippingAddressRepository;

    /**
     * @var ObjectManager $objectManager
     */
    private $objectManager;

    /**
     * ShippingAddressApiController constructor.
     * @param ShippingAddressRepository $shippingAddressRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ShippingAddressRepository $shippingAddressRepository,
        ObjectManager $objectManager
    )
    {
        $this->shippingAddressRepository = $shippingAddressRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * post Shipping Address by id.
     * <strong>Simple example:</strong><br />
     * http://endpoint/api/shippings/addresses <br>.
     *
     * @ApiDoc(
     * resource = true,
     * description = "post Shipping Address by id",
     * authentication=true,
     *  parameters={
     *
     *  },
     * statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Bad request"
     * },
     * section="Shipping Address"
     * )
     *
     * @RestView()
     *
     * @return Response|View
     *
     * @IsGranted({"ROLE_ADMIN", "ROLE_USER"})
     * @throws NotFoundHttpException when not exist
     *
     */
    public function postShippingAddressAction()
    {
        try {
            /** @var ShippingAddress $model */
            $model = $this->objectManager->startProcessingEntity(
                ShippingAddress::class,
                'request',
                [ShippingAddress::GROUP_POST]
            );
            $this->shippingAddressRepository->save($model);

            return $this->createSuccessResponse(
                $model
            );
        } catch (ValidatorException $e) {
            $view = $this->view($e->getErrors(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $view = $this->view((array)$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }
}
