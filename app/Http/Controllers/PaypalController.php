<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\User;
use Session;
use Illuminate\Support\Facades\Auth;
use Srmklive\PayPal\Services\ExpressCheckout;
class PaypalController extends Controller
{
    protected $provider;
    public function __construct() {
    $this->provider = new ExpressCheckout();
    }

    public function expressCheckout(Request $request) {
       $this->validate($request, [
        'amount'     =>  'required|numeric',
       ]);
        $money=$request->get('amount');
        $invoice_id = Invoice::count() + 1;
        $cart = $this->getCart($invoice_id,$money);
      
        $invoice = new Invoice();
        $invoice->userid=Auth::user()->id;

        $invoice->title = $cart['invoice_description'];
        $invoice->price = $cart['total'];
        $invoice->save();

        $response = $this->provider->setExpressCheckout($cart);

        if (!$response['paypal_link']) {
          return redirect('/home')->with(['code' => 'danger', 'message' => 'Something went wrong with PayPal']);
          
        }
 
        return redirect($response['paypal_link']);
      }


      private function getCart( $invoice_id, $money)
    {
        return [

            'items' => [
                [
                    'name' => 'Donation',
                    'price' => $money,
                    'qty' => 1,
                ],
                
            ],

            'return_url' => url('/paypal/express-checkout-success'),

            'invoice_id' => config('paypal.invoice_prefix') . '_' . $invoice_id,
            'invoice_description' => "Order #" . $invoice_id . " Invoice",
            'cancel_url' => url('/home'),

            'total' => $money
        ];
    }


    public function expressCheckoutSuccess(Request $request) {


        $token = $request->get('token');

        $PayerID = $request->get('PayerID');

        $response = $this->provider->getExpressCheckoutDetails($token);

        if (!in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            return redirect('/home')->with(['code' => 'danger', 'message' => 'Error processing PayPal payment']);
        }

        $invoice_id = explode('_', $response['INVNUM'])[1];
        $money=$response['AMT'];

        $cart = $this->getCart($invoice_id,$money);

            $payment_status = $this->provider->doExpressCheckoutPayment($cart, $token, $PayerID);
            $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];

        $invoice = Invoice::find($invoice_id);

        $invoice->payment_status = $status;

        $invoice->save();

        if ($invoice->paid) {
            \Session::put('invoice_id',$invoice_id );
            \Session::put('invoice_amt',$invoice->price );
            \Session::put('invoice_title',$invoice->title );
            \Session::put('invoice_uid',$invoice->userid );
            return redirect('/receipt/email')->with(['code' => 'success', 'message' => 'Thank You !! We receieved $ '. $invoice->price.' Order ' . $invoice->id . ' has been paid successfully!']);
        }
        
        return redirect('/home')->with(['code' => 'danger', 'message' => 'Error processing PayPal payment for Order ' . $invoice->id . '!']);
    }
}


