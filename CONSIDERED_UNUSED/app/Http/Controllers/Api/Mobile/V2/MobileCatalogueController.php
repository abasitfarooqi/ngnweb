<?php

namespace App\Http\Controllers\Api\Mobile\V2;

use App\Http\Controllers\Api\Mobile\V2\Concerns\RewritesApiPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileCatalogueController extends \App\Http\Controllers\Api\Mobile\MobileCatalogueController
{
    use RewritesApiPayload;

    public function homeFeed(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::homeFeed($request));
    }

    public function branches(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::branches());
    }

    public function bikes(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::bikes($request));
    }

    public function newBikeDetail(int $id): JsonResponse
    {
        return $this->rewriteApiPayload(parent::newBikeDetail($id));
    }

    public function usedBikeDetail(int $id): JsonResponse
    {
        return $this->rewriteApiPayload(parent::usedBikeDetail($id));
    }

    public function rentals(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::rentals($request));
    }

    public function rentalDetail(int $id): JsonResponse
    {
        return $this->rewriteApiPayload(parent::rentalDetail($id));
    }

    public function services(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::services());
    }

    public function shopProducts(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::shopProducts($request));
    }

    public function shopProductDetail(string $idOrSlug): JsonResponse
    {
        return $this->rewriteApiPayload(parent::shopProductDetail($idOrSlug));
    }

    public function shopFilters(): JsonResponse
    {
        return $this->rewriteApiPayload(parent::shopFilters());
    }

    public function spareParts(Request $request): JsonResponse
    {
        return $this->rewriteApiPayload(parent::spareParts($request));
    }
}
