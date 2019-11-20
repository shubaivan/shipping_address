<?php

namespace App\Controller\Api;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

class AbstractRestController extends AbstractFOSRestController
{
    const REQUEST_HEADER_APPLICATION_JSON = 'application/json';

    /**
     * @param $data
     * @param null|array $groups
     * @param null|bool $withEmptyField
     *
     * @return View
     */
    protected function createSuccessResponse($data, array $groups = null, $withEmptyField = null)
    {
        $context = new Context();
        if ($groups) {
            $context->setGroups($groups);
        }

        if ($withEmptyField) {
            $context->setSerializeNull(true);
        }

        return View::create()
            ->setStatusCode(Response::HTTP_OK)
            ->setData($data)
            ->setContext($context);
    }
}
