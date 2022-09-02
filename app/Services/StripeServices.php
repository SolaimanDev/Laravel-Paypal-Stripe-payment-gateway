<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Traits\ConsumeExternalServices;

class StripeServices
{
    use ConsumeExternalServices;
    protected $baseUri;

    protected $key;

    protected $secret;

    protected $plans;

    public function __construct()
    {
        $this->baseUri = config('services.stripe.base_uri');
        $this->key = config('services.stripe.key');
        $this->secret = config('services.stripe.secret');
        // $this->plans = config('services.stripe.plans');
    }
    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }
    public function decodeResponse($response)
    {
        return json_decode($response);
    }
    public function resolveAccessToken()
    {
        // $credentials = base64_encode("{$this->key}:{$this->secret}");

        return "Bearer {$this->secret}";
    }
    public function handlePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);
        $intent = $this->createIntent($request->value, $request->currency, $request->payment_method);
        session()->put('paymentIntentId', $intent->id);
        return redirect()->route('approval');
    }
    public function handleApproval()
    {
        if (session()->has('paymentIntentId')) {
            $paymentIntentId = session()->get('paymentIntentId');
            $confirmation = $this->confirmPayment($paymentIntentId);
            if ($confirmation->status === 'succeeded') {
                $name = $confirmation->charges->data[0]->billing_details->name;
                $currency = strtolower($confirmation->currency);
                $amount = $confirmation->amount / $this->resolveCurrencyFactor($currency);
                return redirect()
                    ->route('home')
                    ->withSuccess(['payment' => "Thanks,{$name}. We recieved your {$amount} {$currency} payment."]);
            }
        }
        return redirect()->route('home')
            ->withErrors('We are unable to confirm your payment. Try again, please');
    }
    public function createOrder($value, $currency)
    {
        // 
    }
    public function capturePayment($approvalId)
    {
        // 
    }
    public function createIntent($value, $currency, $paymentMethod)
    {
        return $this->makeRequest('POST', '/v1/payment_intents', [], [
            'amount' => round($value * $this->resolveCurrencyFactor($currency)),
            'currency' => strtolower($currency),
            'payment_method' => $paymentMethod,
            'confirmation_method' => 'manual'
        ]);
    }
    public function confirmPayment($paymentIntentId)
    {
        return $this->makeRequest('POST', "/v1/payment_intents/{$paymentIntentId}/confirm");
    }
    public function resolveCurrencyFactor($currency)
    {
        $zeroDecimalCurrencies = ['JPY'];
        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return 1;
        }
        return 100;
    }
}
