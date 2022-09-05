<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\PaymentPlatform;
use App\Models\Subscription;
use App\Resolvers\PaymentPlatformResolver;

class SubscribeController extends Controller
{
    protected $paymentPlatformResolver;
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->middleware(['auth', 'unsubscribed']);
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function index()
    {
    }
    public function show()
    {
        $platforms = PaymentPlatform::where('subscriptions_enabled', true)->get();
        $plans = Plan::all();
        return view('subscribe', compact('platforms', 'plans'));
    }
    public function store(Request $request)
    {
        $rulse = [
            'plan' => ['required', 'exists:plans,slug'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];
        $request->validate($rulse);

        $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService($request->payment_platform);
        session()->put('subscriptionPlatformId', $request->payment_platform);
        return $paymentPlatform->handleSubscription($request);
    }
    public function approval(Request $request)
    {
        $rulse = [

            'plan' => ['required', 'exists:plans,slug'],
        ];
        $request->validate($rulse);

        if (session()->has('subscriptionPlatformId')) {
            $paymentPlatform = $this->paymentPlatformResolver
                ->resolveService(session()->get('subscriptionPlatformId'));

            if ($paymentPlatform->validateSubscription($request)) {

                $plan = Plan::where('slug', $request->plan)->firstOrFail();
                $user = $request->user();
                $subscription = Subscription::create([
                    'active_until' => now()->addDays($plan->duration_in_days),
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,

                ]);
                return redirect()
                    ->route('home')
                    ->withSuccess(['payment' => "Thanks, {$user->name}. You have now {$plan->slug} subscription. Start using now."]);
            }
        }
        return redirect()
            ->route('subscribe.show')
            ->withErrors("We cannot check your subscription. Try again, please.");
    }
    public function cancelled()
    {
        return redirect()
            ->route('subscribe.show')
            ->withErrors("You cancelled payment. Come back whenever you\'re ready ");
    }
}
