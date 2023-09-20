<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    
    /**
     * A basic test example.
     */
    public function test_successful_admin_registration(): void
    {
        $userData = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345",
            "number" => "12213232" ,
        ];

        $this->json('POST', '/api/admin/register', $userData, ['Accept' => 'application/json'])
            ->decodeResponseJson();

            // ->assertStatus(422)
            // ->assertJsonStructure([
            //     "user" => [
            //         'id',
            //         'name',
            //         'email',
            //         'created_at',
            //         'updated_at',
            //     ],
            //     "access_token",
            //     "status",
            //     "message",
            //     "data" => [
            //         "user" =>[
            //             "id",
            //             "name",
            //             "email",
            //             "created_at",
            //             "updated_at"
            //         ]
            //     ]
            // ]);
    }
}
