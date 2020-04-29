<?php

namespace Tests\Unit\Client\Account;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Model\Order\Invoice;
use App\Model\Order\Order;
use App\Model\Product\Product;
use Tests\DBTestCase;

class DownloadApiTest extends DBTestCase
{
    use DatabaseTransactions;


    /** @group product-download */
    public function test_downloadValidation_whenValidParamasPassed_returnstrue()
    {
        $this->withoutMiddleware();
        $this->getLoggedInUser();
        $user_id = $this->user->id;
        $product = factory(Product::class)->create();
        $invoice = factory(Invoice::class)->create(['user_id'=> $user_id]);
        $order = factory(Order::class)->create(['client'=> $user_id,'invoice_id'=>$invoice->id,'product'=>$product->id]);
        $cont = new \App\Http\Controllers\Product\ExtendedBaseProductController();
        $response = $this->getPrivateMethod($cont,'downloadValidation',['true',$product->id,$invoice->number]);
        $this->assertEquals($response,true);
    }

    /** @group product-download */
    public function test_downloadValidation_whenInValidProductIdPassed_returnsFalse()
    {
        $this->withoutMiddleware();
        $this->getLoggedInUser();
        $user_id = $this->user->id;
        $product = factory(Product::class)->create();
        $invoice = factory(Invoice::class)->create(['user_id'=> $user_id]);
        $order = factory(Order::class)->create(['client'=> $user_id,'invoice_id'=>$invoice->id,'product'=>$product->id]);
        $cont = new \App\Http\Controllers\Product\ExtendedBaseProductController();
        $response = $this->getPrivateMethod($cont,'downloadValidation',['true','1223434',$invoice->number]);
        $this->assertEquals($response,false);
    }

    /** @group product-download */
    public function test_downloadValidation_whenInValidInvoiceNoPassed_returnsFalse()
    {
        $this->withoutMiddleware();
        $this->getLoggedInUser();
        $user_id = $this->user->id;
        $product = factory(Product::class)->create();
        $invoice = factory(Invoice::class)->create(['user_id'=> $user_id]);
        $order = factory(Order::class)->create(['client'=> $user_id,'invoice_id'=>$invoice->id,'product'=>$product->id]);
        $cont = new \App\Http\Controllers\Product\ExtendedBaseProductController();
        $response = $this->getPrivateMethod($cont,'downloadValidation',['true',$product->id,'2222']);
        $this->assertEquals($response,false);
    }

    /** @group product-download */
    public function test_downloadValidation_whenNoOrdersAttachedToAnInvoice_returnsFalse()
    {
        $this->withoutMiddleware();
        $this->getLoggedInUser();
        $user_id = $this->user->id;
        $product = factory(Product::class)->create();
        $invoice = factory(Invoice::class)->create(['user_id'=> $user_id]);
        $cont = new \App\Http\Controllers\Product\ExtendedBaseProductController();
        $response = $this->getPrivateMethod($cont,'downloadValidation',['true',$product->id,$invoice->number]);
        $this->assertEquals($response,false);
    }

    /** @group product-download */
    public function test_downloadValidation_whenUserRoleIsAdmin_returnsTrue()
    {
        $this->withoutMiddleware();
        $this->getLoggedInUser('admin');
        $user_id = $this->user->id;
        $product = factory(Product::class)->create();
        $invoice = factory(Invoice::class)->create(['user_id'=> $user_id]);
        $cont = new \App\Http\Controllers\Product\ExtendedBaseProductController();
        $response = $this->getPrivateMethod($cont,'downloadValidation',['true',$product->id,$invoice->number]);
        $this->assertEquals($response,true);
    }
}
