@extends('frontend.layouts.user')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icomoon-free:profile"></span> <div class="mx-2">Single Standing Donation records </div>
            <a href="{{ route('user.standingrecord') }}"><button type="button" class="btn btn-success">back</button></a>
        </div>
    </section>
  <section class="px-4">
    <div class="row  my-3">

        <div class="col-md-12">

                {{-- Current order start  --}}

                          <section class="px-4"  id="contentContainer">
                            <div class="row my-3">
                                <div class="stsermsg"></div>
                                <div class="col-md-12 mt-2 text-center">
                                    <div class="overflow">
                                        <table class="table table-custom shadow-sm bg-white" id="example">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>amount</th>
                                                    <th>Donation receiver, Charity, Campaign</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @forelse ($data as $data)
                                                    <tr>
                                                        <td>{{ Carbon::parse($data->donation_date)->format('d/m/Y')}}</td>
                                                        <td>£{{$data->d_amount}}</td>
                                                        <td>{{$data->d_title }}</td>
                                                    </tr>
                                                @empty


                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>


                {{-- current order end  --}}


        </div>
    </div>
  </section>
</div>

@endsection