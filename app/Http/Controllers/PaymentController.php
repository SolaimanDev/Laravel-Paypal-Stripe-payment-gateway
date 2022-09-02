<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaypalServices;
use App\Resolvers\PaymentPlatformResolver;

class PaymentController extends Controller
{
    protected $paymentPlatformResolver;
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->middleware('auth');
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }
    public function pay(Request $request)
    {
        // dd($request->all());
        $rulse = [
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];
        $request->validate($rulse);
        $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService($request->payment_platform);
        session()->put('paymentPlatformId', $request->payment_platform);
        return $paymentPlatform->handlePayment($request);
    }
    public function approval()
    {

        if (session()->has('paymentPlatformId')) {
            $paymentPlatform = $this->paymentPlatformResolver->resolveService(session()->get('paymentPlatformId'));
            return $paymentPlatform->handleApproval();
        }
        return redirect()
            ->route('home')
            ->withErrors("You cannot retrieve your payment platform. Try again, please");
    }
    public function cancelled()
    {
        return redirect()
            ->route('home')
            ->withErrors("You cancelled Payment");
    }
}
