<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Order Controller
 */
class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Get user orders",
     *     description="Returns list of authenticated user's orders",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by order status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "processing", "completed", "cancelled"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="total_price", type="number", format="float", example=299.99),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->orders();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->with('items')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Create new order",
     *     description="Create order from cart items",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="shipping_info",
     *                 type="object",
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="city", type="string"),
     *                 @OA\Property(property="zip", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or insufficient stock"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_info' => 'required|array',
            'shipping_info.address' => 'required|string',
            'shipping_info.city' => 'required|string',
            'shipping_info.zip' => 'required|string'
        ]);

        try {
            $order = $this->orderService->createOrder(
                $request->user()->id,
                $validated['items'],
                $validated['shipping_info']
            );

            return response()->json([
                'success' => true,
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get order details",
     *     description="Returns single order with items",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not your order"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = $request->user()->orders()->with('items')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or access denied'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}/cancel",
     *     tags={"Orders"},
     *     summary="Cancel order",
     *     description="Cancel pending order",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot cancel this order"
     *     )
     * )
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->cancelOrder($id);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
