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
  <section class="">
    <div class="row  my-3">

        <div class="col-md-12">

                {{-- Current order start  --}}

                          <section class="px-4"  id="contentContainer">
                            <div class="row my-3">
                                <div class="stsermsg"></div>
                                <div class="col-md-12 mt-2 text-center  shadow-sm">
                                    <div class="overflow pt-3">
                                        <table class="table " id="example">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Beneficiary</th>
                                                    <th>amount</th>
                                                    <th>Instalment Mode</th>
                                                    <th>Instalment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @forelse ($singleStddonation as $data)
                                                    <tr>
                                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                                        <td>{{$data->charity->name}}</td>
                                                        <td>£{{$data->amount}}</td>
                                                        <td>{{$data->instalment_mode }}</td>
                                                        <td>{{$data->instalment_date}}</td>
    
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