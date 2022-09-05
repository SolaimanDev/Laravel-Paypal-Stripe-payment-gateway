@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Make Payment</div>

                <div class="card-body">
                    <form action="{{route('pay')}}" method="post" id="paymentForm">
                        @csrf
                        <div class="row">
                            <div class="col-auto">
                                <label>How much you want to pay?</label>
                                <input type="number" min="5" step="0.01" class="form-control" name="value" value="{{ mt_rand(500, 100000) / 100 }}" required>
                                <small class="form-text text-muted">
                                    Use values with up to two decimal positions, using a dot "."
                                </small>
                            </div>
                            <div class="col-auto">
                                <label>Currency</label>
                                <select class="form-control" name="currency" required>
                                    @foreach ($currencies as $currency)
                                    <option value="{{ $currency->currency }}">
                                        {{ strtoupper($currency->currency) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <label for=""> Select the desired payment platform</label>
                                <div class="form-group" id="toggler">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        @foreach($platforms as $platform)
                                        <label class="btn btn-outline-secondary rounded m-2 p-1" data-target="#{{ $platform->name }}Collapse" data-toggle="collapse">
                                            <input type="radio" name="payment_platform" value="{{$platform->id}}" required>
                                            <img src="{{$platform->image}}" alt="" srcset="" class="img-thumnail">
                                        </label>
                                        @endforeach

                                    </div>
                                    @foreach ($platforms as $platform)
                                    <div id="{{ $platform->name }}Collapse" class="collapse" data-parent="#toggler">
                                        @include ('components.' . strtolower($platform->name) . '-collapse')
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto">
                                <p class="border-bottom border-primary rounded">
                                    @if (! optional(auth()->user())->hasActiveSubscription())
                                    Would you like a discount every time?
                                    <a href="{{route('subscribe.show')}}">Subscribe</a>
                                    @else
                                    You get a <span class="font-weight-bold">10% off</span> as part of your subscription (this will be applied in the checkout).
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" id="payButton" class="btn btn-primary btn-lg">Pay</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection