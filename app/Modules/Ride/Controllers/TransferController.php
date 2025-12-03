<?php

namespace App\Modules\Ride\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ride\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function __construct(
        private TransferService $transferService
    ) {}

    public function getSuggestions(Request $request): JsonResponse
    {
        $bookingId = $request->input('booking_id');
        $location = $request->input('location');

        if (!$bookingId) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ID is required'
            ], 400);
        }

        $suggestions = $this->transferService->getTransferSuggestions($bookingId, $location);

        return response()->json($suggestions);
    }

    public function getAvailable(Request $request): JsonResponse
    {
        $params = $request->only(['from', 'to', 'date', 'passengers']);

        $transfers = $this->transferService->getAvailableTransfers($params);

        return response()->json($transfers);
    }

    public function createBooking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|string',
            'transfer_id' => 'required|integer',
            'pickup_time' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ]);

        $booking = $this->transferService->createTransferBooking(
            $validated['booking_id'],
            $validated['transfer_id'],
            [
                'pickup_time' => $validated['pickup_time'] ?? null,
                'special_requests' => $validated['special_requests'] ?? null,
            ]
        );

        return response()->json($booking, 201);
    }
}
