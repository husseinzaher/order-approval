<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

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

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id', 'order_number', 'total', 'status', 'created_at', 'updated_at'
        ]);
        $this->assertDatabaseHas('orders', [
            'order_number' => $response->json('order_number'),
        ]);
    }

    #[Test]
    public function it_requires_at_least_one_item()
    {
        $data = ['items' => []];
        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(422); // Validation error status
    }

    #[Test]
    public function it_calculates_order_total_correctly()
    {
        $data = [
            'items' => [
                [
                    'product_name' => 'Product A',
                    'quantity' => 3,
                    'price' => 100,
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(201);
        $this->assertEquals(300, $response->json('total'));
    }

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

        $response = $this->postJson('/api/orders', $data);
        $response->assertStatus(201);
        $this->assertEquals('pending_approval', $response->json('status'));
    }
}
