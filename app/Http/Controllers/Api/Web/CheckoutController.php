<?php

namespace App\Http\Controllers\Api\Web;

use Midtrans\Snap;
use App\Models\Cart;
use App\Models\Invoice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CheckoutResource;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // set middleware
        $this->middleware('auth:api_customer');
        
        // set midtrans configuration
        \Midtrans\Config::$serverKey    = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds        = config('services.midtrans.is3ds');
        
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            
            $length = 10;
            $random = '';
            for ($i=0; $i < $length; $i++) { 
                $random .= rand(0,1) ? rand(0,9) :  chr(rand(ord('a'), ord('z')));
            }

            // generate no invoice
            $no_invoice = 'INV-'.Str::upper($random);

            // store invoice
            $invoice = Invoice::create([
                'invoice'           => $no_invoice,
                'customer_id'       => auth()->guard('api_customer')->user()->id,
                'courier'           => $request->courier,
                'courier_service'   => $request->courier_service,
                'courier_cost'      => $request->courier_cost,
                'weight'            => $request->weight,
                'name'              => $request->name,
                'phone'             => $request->phone,
                'city_id'           => $request->city_id,
                'province_id'       => $request->province_id,
                'address'           => $request->address,
                'grand_total'       => $request->grand_total,
                'status'            => 'pending',
            ]);

            // store order by invoice
            foreach (Cart::where('customer_id', auth()->guard('api_customer')->user()->id)->get() as $cart){

                // insert product ke table order
                $invoice->orders()->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $cart->product_id,
                    'qty' => $cart->qty,
                    'price' => $cart->price,
                ]);
            }

            // remove cart by customer
            Cart::with('product')->where('customer_id', auth()->guard('api_customer')->user()->id)->delete();

            // buat transaksi ke midtrans kemudian save snap token nya.
            $payload = [
                'transaction_details' =>[
                    'order_id' => $invoice->invoice,
                    'gross_amount' => $invoice->grand_total,
                ],
                'customer_details' => [
                    'first_name' => $invoice->name,
                    'email' => auth()->guard('api_customer')->user()->email,
                    'phone' => $invoice->phone,
                    'shipping_address' => $invoice->address,
                ],
            ];

            // create snap token
            $snapToken = Snap::getSnapToken($payload);

            // update snap_token
            $invoice->snap_token = $snapToken;
            $invoice->save();

            // make response "snap_token"
            $this->response['snap_token'] = $snapToken;
        });
        return New CheckoutResource(true, 'Checkout Successfully', $this->response);
    }
}
