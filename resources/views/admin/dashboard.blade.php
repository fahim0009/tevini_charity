
@extends('layouts.admin')

@section('content')


<div class="dashboard-content py-2 px-4">
    <div class="rows bg-white shadow-sm my-3">
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".25s" class="wow fadeIn box text-center theme-1 p-3 ">
                    <span class="iconify bg-violet" data-icon="mdi:white-balance-incandescent"></span>
                    <div class="inner theme-txt-violet">
                        <h1 class="my-0 ">£{{$donation}}</h1>
                        <h5 class="my-2 ">Total Donation In</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".30s" class="wow fadeIn box text-center theme-2 p-3 ">
                    <span class="iconify bg-pink" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-pink">
                        <h1 class="my-0 ">£{{$transaction}}</h1>
                        <h5 class="my-2 ">Total Charity Out</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".35s" class="wow fadeIn box text-center theme-yellow p-3 ">
                    <span class="iconify bg-yellow" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-yellow">
                        <h1 class="my-0 ">£{{$voucherout}}</h1>
                        <h5 class="my-2 ">Total Voucher In</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rows bg-white shadow-sm my-3">
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".25s" class="wow fadeIn box text-center theme-1 p-3 ">
                    <span class="iconify bg-violet" data-icon="mdi:white-balance-incandescent"></span>
                    <div class="inner theme-txt-violet">
                        <h1 class="my-0 ">£{{ $commission }}</h1>
                        <h5 class="my-2 ">Total Commission</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="cols">
            <div class="card">
                <div data-wow-delay=".30s" class="wow fadeIn box text-center theme-2 p-3 ">
                    <span class="iconify bg-pink" data-icon="ic:baseline-local-offer"></span>
                    <div class="inner theme-txt-pink">
                        <h1 class="my-0 ">{{$processvoucher}}</h1>
                        <h5 class="my-2 ">Total Voucher process</h5>
                    </div>
                </div>
            </div>
        </div>


    </div>


</div>

@endsection


