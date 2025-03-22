<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    /**
     * Create an order with items.
     *
     * @param array $data Contains an 'items' array with product details.
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $data): Order
    {
        // Enforce that at least one item is provided.
        if (empty($data['items']) || count($data['items']) == 0) {
            throw new Exception("Order must have at least one item.");
        }

        return DB::transaction(function () use ($data) {
            $lastOrder = Order::lockForUpdate()->orderBy('id', 'desc')->first();

            $nextNumber = $lastOrder ? (int)$this->extractNumberFromString($lastOrder?->order_number) + 1 : 1;
            $orderNumber = 'ORD-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_number' => $orderNumber,
                'status' => 'pending',
                'total' => 0, // temporary total
            ]);

            $total = 0;

            foreach ($data['items'] as $itemData) {
                if (empty($itemData['product_name']) || $itemData['quantity'] <= 0 || $itemData['price'] <= 0) {
                    throw new Exception("Invalid item details.");
                }

                $order->items()->create($itemData);
                $total += $itemData['quantity'] * $itemData['price'];
            }

            $order->total = $total;
            if ($total > 1000) {
                $order->status = 'pending_approval';
            }
            $order->save();

            OrderHistory::create([
                'order_id' => $order->id,
                'old_status' => '',
                'new_status' => $order->status
            ]);

            return $order;
        });
    }

    /**
     * Process order approval.
     *
     * @param Order $order
     * @return Order
     * @throws Exception
     */
    public function approveOrder(Order $order): Order
    {
        if ($order->status !== 'pending_approval') {
            throw new Exception("Order is not pending approval.");
        }

        return DB::transaction(function () use ($order) {
            $oldStatus = $order->status;
            $order->status = 'approved';
            $order->save();

            OrderHistory::create([
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => 'approved'
            ]);

            return $order;
        });
    }


    private function extractNumberFromString(string $input): string
    {
        preg_match_all('/\d+/', $input, $matches);

        return isset($matches[0]) ? implode('', $matches[0]) : '';
    }
}
