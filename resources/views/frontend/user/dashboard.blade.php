@extends('frontend.layouts.user')
@section('content')

@php
    $alltransactions = \App\Models\Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->limit(5)->get();


    $tamount = \App\Models\Usertransaction::where([
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=', auth()->user()->id],
                ['pending','=', '1']
                ])->orderBy('id','DESC')->get();

    $pending_transactions = \App\Models\Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
        ])->sum('amount');

    $donation_req = \App\Models\CharityLink::where('email',auth()->user()->email)->where('donor_notification','0')->get();

use Illuminate\Support\Carbon;
@endphp


<div class="row ">
    <div class="col-lg-6">
        <div class="user">
            Welcome, {{auth()->user()->name}}
        </div>
        <div class="ermsg"></div>
        @if (isset($donation_req))
        @foreach ($donation_req as $item)
            <p>You have donation request. Please click <a href="{{route('user.makedonation')}}?cid={{$item->charity_id}}&amount={{$item->amount}}" class="btn-theme bg-primary">here</a>
            <input type="button" value="X" linkid="{{$item->id}}"  class="btn-theme bg-warning close">
            </p>
        @endforeach
        @endif
        <br>
        <h4 class="txt-dash">Account Balance</h4>
        <h2 class="amount">{{auth()->user()->balance}} GBP</h2>
        <p>Pending Balance: {{number_format($pending_transactions, 2)}} GBP</p>
        <div class="row my-2">
            <div class="col-lg-6 ">
                <img src="{{ asset('assets/user/images/card.png') }}" class="img-fluid mt-3 mb-2" alt="">
            </div>
            @if (Auth::user()->accountno)
            <div class="col-lg-6  pt-3 d-flex flex-column px-4">
                
                <a href="{{ route('user.makedonation') }}" class="btn-theme bg-primary">Make a
                    donation</a>
                <a href="{{ route('user.orderbook') }}" class="btn-theme bg-secondary">Order voucher books</a>
                <a href="{{ route('stripeDonation')}}" class="btn-theme bg-ternary">Top up account</a>
                
            </div>
            <div class="col-lg-12">
                <a href="{{ route('userCardService')}}" class="btn-theme bg-primary d-block fs-14 txt-theme fw-bold">Order a card</a>
            </div>
            @endif
        </div>
        <div class="  p-4 py-5 mt-2" style="background-color: #D9D9D9;">
            <div>
                <div class="txt-secondary fs-32 fw-bold  text-center">GIFT AID DONATIONS</div>  <br>
                <div class="txt-secondary fs-20"> Expected gift aid : £{{ Auth::user()->expected_gift_aid }}</div>
                <div class="txt-secondary fs-20"> Gift Aid donations for this Tax Year : £{{ $currentyramount }}</div>
                <div class="txt-secondary fs-20"> Gift Aid donations for last Tax Year : £{{ $totalamount }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row mb-5">
            <div class="col-lg-6">
                <div class="user">
                    Latest transactions
                </div>

            </div>
            <div class="col-lg-6 text-center">
                <a href="{{ route('user.transaction')}}" class="btn-theme bg-ternary">View all transactions</a>
            </div>
        </div>


        <div class="data-container">
            <table class="table table-theme mt-4">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Description</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Balance</th>
                    </tr>
                </thead>

                <?php
                $tbalance = 0;
                ?>

                @foreach ($tamount as $data)
                    @if($data->commission != 0)
                        @php
                        $tbalance = $tbalance - $data->commission;
                        @endphp
                    @endif

                    @php
                    if($data->t_type == "In"){
                        if($data->commission != 0){

                        $tbalance = $tbalance + $data->amount + $data->commission;
                        }else {

                        $tbalance = $tbalance + $data->amount;
                        }

                    }
                    @endphp

                    @php
                    if($data->t_type == "Out"){
                    $tbalance = $tbalance - $data->amount;
                    }
                    @endphp
                @endforeach


                <tbody>
                    @foreach ($alltransactions as $data)
                        @if($data->commission != 0)

                            <tr>
                                <td>{{Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fs-20 txt-secondary fw-bold"></span>
                                        <span class="fs-16 txt-secondary">Commission</span>
                                    </div>
                                </td>
                                <td>-£{{$data->commission}}</td>
                                <td>£{{ number_format($tbalance, 2) }}</td>
                                @php
                                $tbalance = $tbalance + $data->commission;
                                @endphp
                            </tr>
                        @endif

                        <tr>
                            <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fs-20 txt-secondary fw-bold">@if($data->charity_id){{ $data->charity->name}}@endif</span>
                                    <span class="fs-16 txt-secondary">{{$data->title}}</span>
                                </div>
                            </td>
                            @if($data->t_type == "In")

                                @if($data->commission != 0)
                                    <td class="fs-16 info txt-primary">
                                        £{{ number_format($data->amount + $data->commission, 2) }}
                                    </td>
                                    <td class="fs-16 txt-secondary">
                                        £{{ number_format($tbalance, 2) }}
                                    </td>
                                    @php $tbalance = $tbalance - $data->amount - $data->commission; @endphp
                                @else

                                    <td class="fs-16 info txt-primary">
                                        £{{number_format($data->amount, 2)}}
                                    </td>
                                    <td class="fs-16 txt-secondary">
                                        £{{ number_format($tbalance, 2) }}
                                    </td>
                                    @php $tbalance = $tbalance - $data->amount; @endphp
                                @endif

                            @elseif($data->t_type == "Out")
                                <td class="fs-16 info" class="info">
                                    -£{{number_format($data->amount, 2) }}
                                </td>
                                <td class="fs-16 txt-secondary">
                                    £{{ number_format($tbalance, 2) }}
                                </td>
                                @if($data->pending != "0")
                                @php  $tbalance = $tbalance + $data->amount;  @endphp
                                @endif

                            @endif
                        </tr>

                        @endforeach

                        <tr>
                            <td></td>
                            <td></td>
                            <td>Previous Balance</td>
                            <td>£{{ number_format($tbalance, 2) }}</td>
                        </tr>

                </tbody>
            </table>
        </div>




    </div>

</div>
@endsection

@section('script')


<script>
    $(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        //  make mail start
        var url = "{{URL::to('/user/close-a-link')}}";
        $(".close").click(function(){
            linkid = $(this).attr('linkid');
            $.ajax({
                url: url,
                method: "POST",
                data: {linkid},
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                        window.setTimeout(function(){location.reload()},2000)
                    }
                },
                error: function (d) {
                    console.log(d);
                }
            });                

        });
        // send mail end =
    });
</script>

@endsection
