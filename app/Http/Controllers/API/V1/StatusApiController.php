<?php


namespace App\Http\Controllers\API\V1;


use App\Constraints\CampaignStatusConstraint;
use Illuminate\Http\JsonResponse;

class StatusApiController extends BaseController
{
    public function list(): JsonResponse
    {
        return $this->sendResponse(CampaignStatusConstraint::getAll(), 'Status list');
    }
}
