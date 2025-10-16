<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePromotionRequest;
use App\Services\PromotionService;
use Illuminate\Http\JsonResponse;

class CreatePromotionController extends Controller
{
    public function __construct(
        protected PromotionService $promotionService
    ) {}

    /**
     * Handle the incoming request to create a new promotion.
     */
    public function __invoke(CreatePromotionRequest $request): JsonResponse
    {
        // Use the service to create and validate the promotion
        $promotion = $this->promotionService->createPromotion($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Promotion created successfully',
            'data' => $promotion,
        ], 201);
    }
}
