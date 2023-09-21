<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

class AdminFeatureTest extends TestCase
{
    //protected  $token;
    //use RefreshDatabase;

    private $token;

    public function setUp(): void
    {
        parent::setUp();

        // Create a user and generate a token for them
       // $user = Admin::factory()->create();
        $this->token = "";
    }
    
    /**
     * A basic feature test example.
     */
    public function test_reset_table(): void
    {
        Artisan::call('migrate:fresh');
        $this->assertTrue(true);
    }
    
    /**
     * A basic test example.
     */
    public function test_successful_admin_registration(): void
    {
        $userData = [
            "name" => "John Snow",
            "email" => "snow@example.com",
            "password" => "demo12345",
            "number" => "1221323245" ,
        ];

         $op =$this->json('POST', '/api/admin/register', $userData, ['Accept' => 'application/json']);
        // dump($op->decodeResponseJson());
            
         $op->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "user" =>[
                        "id",
                        "name",
                        "email",
                        "created_at",
                        "updated_at"
                    ]
                ]
            ]);
    }

    public function test_successful_admin_login()
    {
        $userData = [
            "email" => "snow@example.com",
            "password" => "demo12345",
        ];

         $response =$this->json('POST', '/api/admin/login', $userData, ['Accept' => 'application/json']);
        // dump($response->decodeResponseJson());                    
         $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "user" =>[
                        "id",
                        "name",
                        "email",
                        "email_verified_at",
                        "created_at",
                        "updated_at"
                    ],
                    "token"
                ]
            ]);

        $storedResponse = $response->decodeResponseJson();
       
        global $adminToken;

        $adminToken = $storedResponse['data']['token'];
      
    }

    public function test_successful_user_registration(): void
    {
        $userData = [
            "name" => "Test Snow",
            "email" => "test@snow.com",
            "password" => "snow12345",
            "number" => "8912345670" ,
        ];

         $op =$this->json('POST', '/api/user/register', $userData, ['Accept' => 'application/json']);
        // dump($op->decodeResponseJson());
            
         $op->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "user" =>[
                        "id",
                        "name",
                        "email",
                        "created_at",
                        "updated_at"
                    ]
                ]
            ]);
    }

    public function test_successful_user_login()
    {
        $userData = [
            "email" => "test@snow.com",
            "password" => "snow12345",
        ];

         $response =$this->json('POST', '/api/user/login', $userData, ['Accept' => 'application/json']);
         //dump($response->decodeResponseJson());                    
         $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "user" =>[
                        "id",
                        "name",
                        "email",
                        "email_verified_at",
                        "created_at",
                        "updated_at"
                    ],
                    "token"
                ]
            ]);

        $storedResponse = $response->decodeResponseJson();
       
        global $userToken;

        $userToken = $storedResponse['data']['token'];
      
    }

    public function test_tokens(){
        global $adminToken;
        $atoken = $adminToken;
        //dump($atoken);

        global $userToken;
        $utoken = $userToken;
        //dump($utoken);

        $this->assertTrue(true);
    }

    public function test_user_create_loan(){

        global $userToken;
        $utoken = $userToken;

        $userData = [
            "amount"=>"700",
            "term"=>"3"
        ];

        $header = ['Accept' => 'application/json' , 'Authorization' => "Bearer " . $userToken ] ;
        
        $response =$this->json('POST', '/api/user/createLoan', $userData, $header );
        //dump($response->decodeResponseJson());                    
        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "loan_data" => [
                        'loan_ref_number',
                        "user_id",
                        "loan_amount",
                        "term",
                        "status",
                        "loan_creation_date",
                        "updated_at",
                        "created_at",
                        "id"
                    ],
                    "repayment_list"
                ]
            ]);
    }

    public function test_admin_get_all_user_loan_list(){
        global $adminToken;
        $atoken = $adminToken;

        $userData = [
            "user_id" => 1 ,
        ];

        $header = ['Accept' => 'application/json' , 'Authorization' => "Bearer " . $adminToken ] ;
        
        $response =$this->json('POST', '/api/admin/getAllUserLoanList', $userData, $header );
         //dump($response->decodeResponseJson());                    
       $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "loan_data" =>[
                    ],
                ]
            ]);
    }


    public function test_user_single_loan_where_status_is_pending(){
        global $userToken;
        $utoken = $userToken;

        $userData = [
            "loan_id" => 1 ,
        ];

        $header = ['Accept' => 'application/json' , 'Authorization' => "Bearer " . $utoken ] ;
        
        $response =$this->json('POST', '/api/user/userSingleLoan', $userData, $header );
         //dump($response->decodeResponseJson());                    
       $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "loan_data" =>[
                        
                    ],
                    "repayment_data"
                ]
            ]);

        $code = $response->decodeResponseJson();
        if($code['data']['loan_data'][0]['status'] == "PENDING"){
            $this->assertTrue(true);
        }else{
            $this->assertTrue(false);
        }

    }

    public function test_admin_approve_user_loan(){
        global $adminToken;
        $atoken = $adminToken;

        $userData = [
            "user_id" => 1 ,
            "loan_id" => 1,
        ];

        $header = ['Accept' => 'application/json' , 'Authorization' => "Bearer " . $adminToken ] ;
        
        $response =$this->json('POST', '/api/admin/approveSingleLoanByAdmin', $userData, $header );
        // dd($response->decodeResponseJson());                    
        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "loan_data" =>[
                    ],
                ]
            ]);

        $code = $response->decodeResponseJson();
        if($code['message'] == "Single Loan Status Approved"){
            $this->assertTrue(true);
        }else{
            $this->assertTrue(false);
        }   
    }

    public function test_user_single_loan_where_status_is_approved(){
        global $userToken;
        $utoken = $userToken;

        $userData = [
            "loan_id" => 1 ,
        ];

        $header = ['Accept' => 'application/json' , 'Authorization' => "Bearer " . $utoken ] ;
        
        $response =$this->json('POST', '/api/user/userSingleLoan', $userData, $header );
         //dump($response->decodeResponseJson());                    
        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "message",
                "data" => [
                    "loan_data" =>[
                        
                    ],
                    "repayment_data"
                ]
            ]);

        $code = $response->decodeResponseJson();
        if($code['data']['loan_data'][0]['status'] == "APPROVED"){
            $this->assertTrue(true);
        }else{
            $this->assertTrue(false);
        }

    }




}
