<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentPlatform;

class SubscribeController extends Controller
{
    
    $paymentPlatforms = PaymentPlatform::all();
    $plans =Plan::all();
    return view('subscribe',compact('paymentPlatforms','plans'));

       
}
