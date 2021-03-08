<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        $response = $this->post(
            route("sanctum_login"),
            ["email" => $user->email, "password" => "password"]
        );


        $response->assertStatus(200)
            ->assertJson(['token' => true,]);

        $token = $response->json("token");
        $this->withHeader("Authorization", "Bearer $token");


        $testResponse = $this->get(route("me"));

        $testResponse->assertStatus(200)->assertJsonFragment([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);

        $this->assertAuthenticated("sanctum");


        $testResponse = $this->post(route("sanctum_logout"));
        $testResponse->assertNoContent();



        $this->assertAuthenticated("sanctum"); // should fail but passes





        $testResponse = $this->get(route("me"));

        $this->assertAuthenticated("sanctum");// should fail but passes

//        this should fail as user is already logged out but old user is retrieved
        $testResponse->assertStatus(200)->assertJsonFragment([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);

//        notice user does not have any token in db
        $this->assertFalse($user->tokens()->exists());

//        another post to logout
        $testResponse = $this->post(route("sanctum_logout"));
//        this should fail but it passes
        $testResponse->assertNoContent();

    }
}
