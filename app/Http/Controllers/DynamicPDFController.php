<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use PDF;
use Mail;
use App\User;
class DynamicPDFController extends Controller
{
    function index()
    {
         $paymentdata =  $this->get_payment_data();
         return view('\receipt')->with($paymentdata);
    }

    function get_payment_data()
    {
        $user_info = User::find(\Session::get('invoice_uid'));

        $paymentdata = array(
            'invoice_id' => \Session::get('invoice_id'),
             'invoice_title' => \Session::get('invoice_title' ),
            'invoice_amt' => \Session::get('invoice_amt' ),
            'user_name'  =>$user_info->name,
            'user_email'  =>$user_info->email,
         );
     return  $paymentdata;
    }
    function pdf()
    {
     $pdf = \App::make('dompdf.wrapper');
     $pdf->loadHTML($this->convert_payment_data_to_html());
     return $pdf->stream();
    }

    function convert_payment_data_to_html()
    {
     $paymentdata = $this->get_payment_data();
     $output = '
      <h3 align="left">Dear '.$paymentdata['user_name'].',</h3>
      <h3 align="left">'.$paymentdata['user_email'].',</h3>
     <h3 align="left">'.$paymentdata['invoice_title'].',</h3>
     <br>
     <h3 align="left">Thank you for your generous help in the amount of $'.$paymentdata['invoice_amt'].'</h3>
     <h3 align="left">Sincerely,</h3>
     <h3 align="left">Grow Together Foundation</h3>';
     return $output;
    }
    function send_email()
    {
        $paymentdata = $this->get_payment_data();
        Mail::send('email', $paymentdata, function($message) use($paymentdata)
        {
            $message->from('growtogether@test.com', 'Grow Together');
        
            $message->to($paymentdata['user_email'])->subject('Receipt Of Payment');
        
            $message->attachData($this->pdf(), "receipt.pdf");
        });
        return redirect('/home')->with(['code' => 'success', 'message' => 'Thank You !! We receieved $ '.$paymentdata['invoice_amt'].' Order ' . $paymentdata['invoice_id'] . ' has been paid successfully!']);
    }
}
