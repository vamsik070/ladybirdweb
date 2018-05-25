<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class UserTest extends TestCase
{
	use DatabaseTransactions;
    
     /** @group clientController */
    public function test_clientController_whenUserUpdatesDetails()
    {
        $user = factory(User::class)->create();
         $this->withoutMiddleware();
        $response = $this->call('PATCH','clients/'.$user->id, ['first_name'=>$user->first_name,'last_name'=> $user->last_name,'email'=>$user->email,'user_name'=>$user->user_name,'company'=>$user->company,'bussiness'=>$user->bussiness,'active'=>$user->active,'mobile_verified'=>$user->mobile_verified,'role'=>$user->role,'position'=>'','company_type'=>$user->company_type,'company_size'=>$user->company_size,'address'=>$user->address,'town'=>$user->town,'country'=>$user->country,'state'=>$user->state,'zip'=>$user->zip,'timezone_id'=>'79','currency'=>'USD','mobile_code'=>'91','mobile'=>$user->mobile,'skype'=>'',
        	'manager'=>'Ashutosh']);
       $response->assertSessionHas("success");
      
    }
}
