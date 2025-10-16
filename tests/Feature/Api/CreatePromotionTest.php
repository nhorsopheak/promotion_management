<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePromotionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_promotion_with_valid_data()
    {
        $promotionData = [
            'code' => 'SUMMER2024',
            'name' => 'Summer Sale 2024',
            'description' => 'Get amazing discounts this summer',
            'type' => 'percentage_discount',
            'status' => 'active',
            'start_date' => '2024-06-01 00:00:00',
            'end_date' => '2024-08-31 23:59:59',
            'days_of_week' => [1, 2, 3, 4, 5],
            'conditions' => [
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'apply_to_type' => 'all',
            ],
        ];

        $response = $this->postJson('/api/v1/promotions', $promotionData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Promotion created successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'code',
                    'name',
                    'description',
                    'type',
                    'status',
                    'start_date',
                    'end_date',
                    'days_of_week',
                    'conditions',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('promotions', [
            'code' => 'SUMMER2024',
            'name' => 'Summer Sale 2024',
            'type' => 'percentage_discount',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/promotions', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'name', 'type', 'status']);
    }

    /** @test */
    public function it_validates_unique_promotion_code()
    {
        Promotion::factory()->create(['code' => 'EXISTING']);

        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'EXISTING',
            'name' => 'Test Promotion',
            'type' => 'percentage_discount',
            'status' => 'active',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    /** @test */
    public function it_validates_promotion_type()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'TEST123',
            'name' => 'Test Promotion',
            'type' => 'invalid_type',
            'status' => 'active',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function it_validates_promotion_status()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'TEST123',
            'name' => 'Test Promotion',
            'type' => 'percentage_discount',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function it_validates_end_date_is_after_start_date()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'TEST123',
            'name' => 'Test Promotion',
            'type' => 'percentage_discount',
            'status' => 'active',
            'start_date' => '2024-08-31',
            'end_date' => '2024-06-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    /** @test */
    public function it_can_create_buy_x_get_y_free_promotion()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'BUY2GET1',
            'name' => 'Buy 2 Get 1 Free',
            'type' => 'buy_x_get_y_free',
            'status' => 'active',
            'conditions' => [
                'buy_quantity' => 2,
                'get_quantity' => 1,
                'apply_to_type' => 'any',
                'get_type' => 'cheapest',
                'apply_to_cheapest' => true,
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('promotions', [
            'code' => 'BUY2GET1',
            'type' => 'buy_x_get_y_free',
        ]);
    }

    /** @test */
    public function it_can_create_step_discount_promotion()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'STEP50',
            'name' => 'Step Discount 50%',
            'type' => 'step_discount',
            'status' => 'active',
            'conditions' => [
                'discount_tiers' => [
                    ['position' => 2, 'percentage' => 20],
                    ['position' => 3, 'percentage' => 30],
                    ['position' => 5, 'percentage' => 50],
                ],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('promotions', [
            'code' => 'STEP50',
            'type' => 'step_discount',
        ]);
    }

    /** @test */
    public function it_can_create_fixed_price_bundle_promotion()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'BUNDLE99',
            'name' => '3 for $99',
            'type' => 'fixed_price_bundle',
            'status' => 'active',
            'conditions' => [
                'bundle_quantity' => 3,
                'bundle_price' => 99.00,
                'bundle_type' => 'any',
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('promotions', [
            'code' => 'BUNDLE99',
            'type' => 'fixed_price_bundle',
        ]);
    }

    /** @test */
    public function it_validates_buy_x_get_y_free_configuration()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'INVALID',
            'name' => 'Invalid Promo',
            'type' => 'buy_x_get_y_free',
            'status' => 'active',
            'conditions' => [
                'buy_quantity' => 0, // Invalid: must be > 0
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['conditions']);
        
        $this->assertStringContainsString('quantity', $response->json('errors.conditions.0'));
    }

    /** @test */
    public function it_validates_step_discount_configuration()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'INVALID',
            'name' => 'Invalid Step',
            'type' => 'step_discount',
            'status' => 'active',
            'conditions' => [
                'discount_tiers' => [], // Invalid: must not be empty
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['conditions']);
        
        $this->assertStringContainsString('tiers', $response->json('errors.conditions.0'));
    }

    /** @test */
    public function it_validates_percentage_discount_configuration()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'INVALID',
            'name' => 'Invalid Discount',
            'type' => 'percentage_discount',
            'status' => 'active',
            'conditions' => [
                'discount_type' => 'percentage',
                'discount_value' => 150, // Invalid: must be <= 100
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['conditions.discount_value']);
    }

    /** @test */
    public function it_validates_fixed_price_bundle_configuration()
    {
        $response = $this->postJson('/api/v1/promotions', [
            'code' => 'INVALID',
            'name' => 'Invalid Bundle',
            'type' => 'fixed_price_bundle',
            'status' => 'active',
            'conditions' => [
                'bundle_quantity' => 1, // Invalid: must be >= 2
                'bundle_price' => 99.00,
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['conditions.bundle_quantity']);
    }
}
