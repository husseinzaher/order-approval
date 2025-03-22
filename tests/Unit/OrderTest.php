<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\OrderService;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;

    public function setUp(): void
    {
        parent::setUp();
        $this->orderService = app(OrderService::class);
    }

    /**
     * @throws \Exception
     */
    #[Test]
    public function it_creates_an_order_successfully()
    {
        $data = [
            'items' => [
                [
                    'product_name' => 'Product A',
                    'quantity' => 2,
                    'price' => 100,
                ],
                [
                    'product_name' => 'Product B',
                    'quantity' => 1,
                    'price' => 50,
                ],
            ]
        ];

        $order = $this->orderService->createOrder($data);

        $this->assertNotNull($order->id);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(250, $order->total);
    }

    #[Test]
    public function it_requires_at_least_one_item()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Order must have at least one item.");

        $data = ['items' => []];
        $this->orderService->createOrder($data);
    }

    /**
     * @throws \Exception
     */
    #[Test]
    public function it_calculates_order_total_correctly()
    {
        $data = [
            'items' => [
                [
                    'product_name' => 'Product A',
                    'quantity' => 3,
                    'price' => 100,
                ],
                [
                    'product_name' => 'Product B',
                    'quantity' => 2,
                    'price' => 50,
                ],
            ]
        ];

        $order = $this->orderService->createOrder($data);

        // Expected total = (3 * 100) + (2 * 50) = 300 + 100 = 400
        $this->assertEquals(400, $order->total);
    }

    /**
     * @throws \Exception
     */
    #[Test]
    public function orders_above_1000_require_approval()
    {
        $data = [
            'items' => [
                [
                    'product_name' => 'Expensive Product',
                    'quantity' => 1,
                    'price' => 1500,
                ]
            ]
        ];

        $order = $this->orderService->createOrder($data);
        $this->assertEquals('pending_approval', $order->status);
    }

    /**
     * @throws \Exception
     */
    #[Test]
    public function it_generates_unique_and_sequential_order_numbers()
    {
        $data1 = [
            'items' => [
                [
                    'product_name' => 'Product A',
                    'quantity' => 1,
                    'price' => 100,
                ]
            ]
        ];
        $order1 = $this->orderService->createOrder($data1);

        $data2 = [
            'items' => [
                [
                    'product_name' => 'Product B',
                    'quantity' => 2,
                    'price' => 200,
                ]
            ]
        ];
        $order2 = $this->orderService->createOrder($data2);

        $this->assertMatchesRegularExpression('/^ORD-\d{8}$/', $order1->order_number);
        $this->assertMatchesRegularExpression('/^ORD-\d{8}$/', $order2->order_number);

        $num1 = (int)substr($order1->order_number, 4);
        $num2 = (int)substr($order2->order_number, 4);


        $this->assertEquals($num1 + 1, $num2);
    }
}
