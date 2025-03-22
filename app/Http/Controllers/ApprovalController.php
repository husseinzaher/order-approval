<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Exception;

class ApprovalController extends Controller
{

    public function __construct(protected OrderService $orderService)
    {

    }

    public function approve(Order $order)
    {
        try {
            $order = $this->orderService->approveOrder($order);
            return response()->json($order);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
