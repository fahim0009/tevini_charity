@extends('frontend.layouts.master')

@section('content')

<section class="contact py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">

                <div class="text-center mb-4">
                    <div style="width:80px;height:80px;border-radius:50%;background:{{ $color }};color:#fff;font-size:40px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        {{ $icon }}
                    </div>
                    <div class="title">{{ $title }}</div>
                </div>

                <div class="ermsg text-center mb-4">
                    <div class="alert" style="background:{{ $color }}15;border-left:4px solid {{ $color }};color:#333;">
                        {{ $message }}
                    </div>
                </div>

                <div class="default p-4" style="background:#f8f9fa;border-radius:8px;">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <div class="paratitle">Voucher No</div>
                            <div class="theme-para" style="color:#222;">{{ $voucher->cheque_no }}</div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="paratitle">Amount</div>
                            <div class="theme-para" style="color:#222;">£{{ number_format($voucher->amount, 2) }}</div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="paratitle">Charity</div>
                            <div class="theme-para" style="color:#222;">{{ $charity?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <div class="paratitle">Date</div>
                            <div class="theme-para" style="color:#222;">{{ $voucher->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="btn-theme bg-primary">Back to Home</a>
                </div>

            </div>
        </div>
    </div>
</section>

<section class="default contactInfo">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Phone</div>
                <p class="theme-para text-center">07490 956 227</p>
                <a href="tel:07490956227" class="btn-theme bg-primary btn-line">Call</a>
            </div>
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Whatsapp</div>
                <p class="theme-para text-center">07490 956 227</p>
                <a href="https://wa.me/447490956227" target="_blank" class="btn-theme bg-primary btn-line">Message</a>
            </div>
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Email</div>
                <p class="theme-para text-center">info@tevini.co.uk</p>
                <a href="mailto:info@tevini.co.uk" class="btn-theme bg-primary btn-line">Email</a>
            </div>
            <div class="col-lg-3 d-flex flex-column align-items-center">
                <div class="paratitle text-center">Address</div>
                <p class="theme-para text-center">5a Holmdale Terrace<br>London N15 6PP</p>
                <a href="https://maps.google.com/?q=5a+Holmdale+Terrace+London+N15+6PP" target="_blank" class="btn-theme bg-primary btn-line">Visit</a>
            </div>
        </div>
    </div>
</section>

@endsection