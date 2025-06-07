<?php

namespace App\Admin\Controllers\Api;

use App\Services\ProductAttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AttributeGroupController extends AdminBaseApiController
{
    public function __construct(protected ProductAttributeService $service) {}

    public function query(Request $request): JsonResponse
    {
        try {
            $data = $this->service->findAttributesByGroupId($request->get('q'));

            return $this->flattenJson($data);
        } catch (NotFoundHttpException $e) {
            return $this->flattenJson([]);
        } catch (\Exception $e) {
            log_error('AttributeGroupController query error' . $e->getMessage());

            return $this->error($e->getMessage());
        }
    }
}
