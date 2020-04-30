@extends('themes.default1.layouts.master')
@section('title')
Invoice
@stop
@section('content-header')
<h1>
View Invoice
</h1>
  <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{url('clients')}}">All Users</a></li>
        <li><a href="{{url('invoices')}}">All Invoices</a></li>
        <li class="active">View Invoice</li>
      </ol>
@stop
@section('content')
<div class="box box-primary">

    <div class="box-header">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('success')}}
        </div>
        @endif
        <!-- fail message -->
        @if(Session::has('fails'))
        <div class="alert alert-danger alert-dismissable">
            <i class="fa fa-ban"></i>
            <b>{{Lang::get('message.alert')}}!</b> {{Lang::get('message.failed')}}.
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{Session::get('fails')}}
        </div>
        @endif
        <div id="response"></div>

        <h4>{{Lang::get('message.invoice')}}
            <!--<a href="{{url('orders/create')}}" class="btn btn-primary pull-right   ">{{Lang::get('message.create')}}</a></h4>-->
    </div>



    <div class="box-body">
        <div class="row">

            <div class="col-md-12">

                <?php $set = App\Model\Common\Setting::where('id', '1')->first(); 
                 $gst =  App\Model\Payment\TaxOption::where('id', '1')->first(); 
                $date = getDateHtml($invoice->date);

                 $symbol = $invoice->currency;
            
                ?>

                <!-- Main content -->
                <section class="invoice">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">
                                <i class="fa fa-globe"></i> {{ucfirst($set->company)}}
                                <small class="pull-right">Date: {!! $date !!}</small>
                            </h2>
                        </div><!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            From
                            <address>

                                <strong>{{$set->company}}</strong><br>
                                {{$set->address}}<br>
                                Phone: {{$set->phone}}<br/>
                                Email: {{$set->email}}
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            To
                            <address>
                                <strong>{{$user->first_name}} {{$user->last_name}}</strong><br>
                                {{$user->address}}<br/>
                                {{$user->town}}<br/>
                                @if(key_exists('name',App\Http\Controllers\Front\CartController::getStateByCode($user->state)))
                                {{App\Http\Controllers\Front\CartController::getStateByCode($user->state)['name']}}
                                @endif
                                {{$user->zip}}<br/>
                                Country : {{App\Http\Controllers\Front\CartController::getCountryByCode($user->country)}}<br/>

                                Mobile: @if($user->mobile_code)<b>+</b>{{$user->mobile_code}}@endif{{$user->mobile}}<br/>
                                Email : {{$user->email}}
                            </address>
                        </div><!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <b>Invoice   #{{$invoice->number}}</b>
                            <br/>

                        </div><!-- /.col -->
                         <div class="col-sm-4 invoice-col">
                            <b>Order</b>   # &nbsp;&nbsp;{!! $order !!}
                            <br/>

                        </div><!-- /.col -->
                         <div class="col-sm-4 invoice-col">
                            <b>GSTIN   &nbsp; #{{$gst->Gst_No}}</b>
                            <br/>

                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Taxes</th>
                                        <th>Tax Rates</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($invoiceItems as $item)
                                    <tr>
                         
                                        <td>{{$item->product_name}}</td>
                                        <td>{{$item->quantity}}</td>
                                        <td>{{currency_format($item->regular_price,$code=$symbol)}}</td>
                                        <td>
                                            <?php $taxes = explode(',', $item->tax_name); ?>
                                            <ul class="list-unstyled">
                                                @forelse($taxes as $tax)
                                                <li>{{$tax}}</li>
                                                @empty 
                                                <li>No Tax</li>
                                                @endif
                                            </ul>
                                        </td>
                                        <td>
                                            <?php $taxes = explode(',', $item->tax_percentage); ?>
                                            <ul class="list-unstyled">
                                                @forelse($taxes as $tax)
                                                <li>{{$tax}}</li>
                                                @empty 
                                                <li>No Tax Rates</li>
                                                @endif
                                            </ul>
                                        </td>
                                     
                                        <td> {{currency_format($item->subtotal,$code=$symbol)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-xs-6">  

                        </div><!-- /.col -->
                        <div class="col-xs-6">
                            <p class="lead">Amount</p>
                            <div class="table-responsive">
                              
                                          <?php
                                $tax_name = [];
                                $tax_percentage = [];
                                foreach ($invoiceItems as $key => $item) {
                                   
                                    if (str_finish(',', $item->tax_name)) {
                                        $name = ($item->tax_name);
                                       
                                    }
                                    if (str_finish(',', $item->tax_percentage)) {
                                        $rate = substr_replace($item->tax_percentage, '', -1);
                                        
                                    }
                                    $tax_name = explode(',', $name);
                                    $tax_percentage = explode(',', $rate);
                                }
                                ?>
                                 <table class="table  table-striped">
                                      @if($invoice->discount != null)
                                  <th>Discount</th>
                                    <td>{{currency_format($invoice->discount,$code=$symbol)}}</td>
                                @endif

                                @if($tax_name[0] !='null' && $tax_percentage[0] !=null)
                                   <?php $productId =  App\Model\Product\Product::where('name',$item->product_name)->pluck('id')->first(); 
                                   $taxInstance= new \App\Http\Controllers\Front\CartController();
                                    $taxes= $taxInstance->checkTax($productId,$user->state,$user->country);
                                     ?>
                                   @if ($currency['currency'] == 'INR' && $user->country == 'IN' && $taxes['tax_attributes'][0]['name']!= 'null')
                                    @if($set->state == $user->state)
                                             <tr class="Taxes">
                            <th>
                                <strong>CGST<span>@</span>{{$taxes['tax_attributes'][0]['c_gst']}}%</strong><br/>
                                <strong>SGST<span>@</span>{{$taxes['tax_attributes'][0]['s_gst']}}%</strong><br/>
                               
                            </th>
                            <td>
                                <?php
                                $cgst = \App\Http\Controllers\Front\CartController::taxValue($taxes['tax_attributes'][0]['c_gst'],$item->regular_price);
                                $sgst = \App\Http\Controllers\Front\CartController::taxValue($taxes['tax_attributes'][0]['s_gst'],$item->regular_price);
                                ?>
                                {{currency_format($cgst,$code=$symbol)}} <br/>
                                {{currency_format($sgst,$code=$symbol)}}<br/>
                             </td>
                              </tr>
                                    @endif
                                      @if($set->state != $user->state && $taxes['tax_attributes'][0]['ut_gst'] == "NULL")
                                      <tr>
                                      <th>
                                    <strong>IGST<span>@</span>{{$taxes['tax_attributes'][0]['i_gst']}}%</strong><br/>
                                  
                            </th>
                            <td>
                                <?php
                                $igst =  \App\Http\Controllers\Front\CartController::taxValue($taxes['tax_attributes'][0]['i_gst'],$item->regular_price);
                                ?>
                                  {{currency_format($igst,$code=$symbol)}} <br/>
                              
                             </td>
                         </tr>
                                     @endif
                                     <tr>
                                     @if($set->state != $user->state && $taxes['tax_attributes'][0]['ut_gst'] != "NULL")
                                     <th>
                                 <strong>UTGST<span>@</span>{{$taxes['tax_attributes'][0]['ut_gst']}}%</strong><br/>
                                 <strong>CGST<span>@</span>{{$taxes['tax_attributes'][0]['c_gst']}}%</strong><br/>

                                  
                            </th>
                            <td>
                                <?php
                                $utgst = \App\Http\Controllers\Front\CartController::taxValue($taxes['tax_attributes'][0]['ut_gst'],$item->regular_price);
                                $cgst = App\Http\Controllers\Front\CartController::taxValue($taxes['tax_attributes'][0]['c_gst'],$item->regular_price)
                                    ?>
                                {{currency_format($utgst,$code=$symbol)}} <br/>
                                {{currency_format($cgst,$code=$symbol)}}

                             </td>
                         </tr>
                                     @endif
                                     @endif
                                      
                                        @if ($currency['currency'] != 'INR')
                                     <tr>
                                        <th>
                                            <strong>{{ucfirst($tax_name[0])}}<span>@</span>{{$tax_percentage[0]}} </strong>
                                        </th>
                                        <td>
                                            <?php
                                            $value = \App\Http\Controllers\Front\CartController::taxValue($tax_percentage[0],$item->regular_price)
                                            ?>
                                             {{currency_format($value,$code=$symbol)}}
                                        </td>

                                    </tr>
                                    @endif
                                    @endif
                                    <th>Total:</th>
                                    <td>{{currency_format($invoice->grand_total,$code=$symbol)}}</td>
                               
                            </table>
                            </div>
                        </div><!-- /.col -->
                    </div><!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print">
                        <div class="col-xs-12"> 
                            <a href="{{url('pdf?invoiceid='.$invoice->id)}}"><button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button></a>
                        </div>
                    </div>
                </section><!-- /.content -->


            </div>
        </div>
    </div>
</div>



@stop