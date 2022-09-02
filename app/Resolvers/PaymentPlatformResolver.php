<?php

namespace App\Resolvers;

use App\Models\PaymentPlatform;
use Exception;

class PaymentPlatformResolver
{
    protected $paymentPlatForms;
    public function __construct()
    {
        $this->paymentPlatForms = PaymentPlatform::all();
    }
    public function resolveService($paymentPlatFormId)
    {
        $name = strtolower($this->paymentPlatForms->firstWhere('id', $paymentPlatFormId)->name);
        $service = config("services.{$name}.class");
        if ($service) {
            return resolve($service);
        }
        throw new \Exception("The selected platform is not the configurations");
    }
}
