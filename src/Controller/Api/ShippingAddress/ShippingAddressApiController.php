<?php

namespace App\Controller\Api\ShippingAddress;

use App\Controller\Api\AbstractRestController;
use App\Entity\ShippingAddress;
use App\Exception\ValidatorException;
use App\Repository\ShippingAddressRepository;
use App\Service\ShippingAddressObjectManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShippingAddressApiController extends AbstractRestController
{
    /**
     * @var ShippingAddressRepository
     */
    private $shippingAddressRepository;

    /**
     * @var ShippingAddressObjectManager $shippingAddressObjectManager
     */
    private $shippingAddressObjectManager;

    /**
     * ShippingAddressApiController constructor.
     * @param ShippingAddressRepository $shippingAddressRepository
     * @param ShippingAddressObjectManager $shippingAddressObjectManager
     */
    public function __construct(
        ShippingAddressRepository $shippingAddressRepository,
        ShippingAddressObjectManager $shippingAddressObjectManager
    )
    {
        $this->shippingAddressRepository = $shippingAddressRepository;
        $this->shippingAddressObjectManager = $shippingAddressObjectManager;
    }

    /**
     * get shippings addresses.
     * <strong>Simple example:</strong><br />
     * http://endpoint/api/shippings/addresses <br>.
     *
     * @ApiDoc(
     * resource = true,
     * description = "get shippings addresses",
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
     * @param ParamFetcher $paramFetcher
     * @return Response|View
     *
     * @IsGranted({"ROLE_ADMIN", "ROLE_USER"})
     *
     * @Rest\QueryParam(name="count", requirements="\d+", default="10", description="Count entity at one page")
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @Rest\QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @Rest\QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @throws NotFoundHttpException when not exist
     *
     */
    public function getShippingAddressDataAction(ParamFetcher $paramFetcher)
    {
        try {
            return $this->createSuccessResponse(
                [
                    'collection' => $this->getShippingAddressRepository()
                        ->getList($paramFetcher, $this->getUser()),
                    'count' => $this->getShippingAddressRepository()
                        ->getList($paramFetcher, $this->getUser(), true)
                ], [ShippingAddress::GROUP_GET]
            );
        } catch (\Exception $e) {
            $view = $this->view((array)$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * post Shipping Address by id.
     * <strong>Simple example:</strong><br />
     * http://endpoint/api/shippings/addresses <br>.
     *
     * @ApiDoc(
     * resource = true,
     * description = "post Shipping Address",
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
            $model = $this->getShippingAddressObjectManager()->createEntity();

            return $this->createSuccessResponse(
                $model, [ShippingAddress::GROUP_GET]
            );
        } catch (ValidatorException $e) {
            $view = $this->view($e->getErrors(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $view = $this->view((array)$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * put Shipping Address by id.
     * <strong>Simple example:</strong><br />
     * http://endpoint/api/shippings/addresses <br>.
     *
     * @ApiDoc(
     * resource = true,
     * description = "put Shipping Address by id",
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
     * @IsGranted({"ROLE_ADMIN", "ROLE_USER"})*
     */
    public function putShippingAddressAction()
    {
        try {
            $model = $this->getShippingAddressObjectManager()->updateEntity();

            return $this->createSuccessResponse(
                $model, [ShippingAddress::GROUP_GET]
            );
        } catch (ValidatorException $e) {
            $view = $this->view($e->getErrors(), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $view = $this->view((array)$e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * @return ShippingAddressRepository
     */
    public function getShippingAddressRepository(): ShippingAddressRepository
    {
        return $this->shippingAddressRepository;
    }

    /**
     * @return ShippingAddressObjectManager
     */
    public function getShippingAddressObjectManager(): ShippingAddressObjectManager
    {
        return $this->shippingAddressObjectManager;
    }
}
