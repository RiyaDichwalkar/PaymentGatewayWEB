

@extends('layouts.app')

@section('content')
 <div class="container">
  <div class="row">
   <div class="col-md-8 col-md-offset-2">

    @if (Session::has('message'))
     <div class="alert alert-{{ Session::get('code') }}">
      <p>{{ Session::get('message') }}</p>
     </div>
    @endif
</div>
</div>
<div class="row">
    <div class="col-md-7" align="right">
     <h4>Customer Data</h4>
    </div>
    <div class="col-md-5" align="right">
     <a href="{{ url('receipt/pdf') }}" class="btn btn-danger">Convert into PDF</a>
    </div>
   </div>
   <br>
   <div class="row">
    <div class="col-md-7" align="right">
     <h4>Customer Data</h4>
    </div>
    <div class="col-md-5" align="right">
     <a href="{{ url('receipt/email') }}" class="btn btn-danger">Send Email</a>
    </div>
   </div>
   <br>
 <h1>Hola</h1>
 
   <h1> {{ $invoice_id}} </h1>
   <h1> {{$invoice_title}} </h1> 
   <h1> {{$invoice_amt}} </h1>

</div>
</div>
    @endsection
<!-- <!DOCTYPE html>
<html>
<head>
</head>
<body>
<div class="container">THANK YOU
 </div>
 </body>
 </html> -->