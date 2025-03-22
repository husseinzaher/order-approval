<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\OrderService;
use Exception;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // Create a new order
    // POST /api/orders
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        try {

            $order = $this->orderService->createOrder($data);
            return response()->json($order, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Get order details including items and history
    // GET /api/orders/{order}
    public function show(Order $order)
    {
        return response()->json($order->load('items', 'histories'));
    }

    // Get order history
    // GET /api/orders/{order}/history
    public function history(Order $order)
    {
        return response()->json($order->histories);
    }
}
