@extends('layouts.admin')
@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')
                    {{-- Current order start  --}}
                              <section class="px-4"  id="contentContainer">
                                <div class="row my-3">

                                    <div class="col-md-12 mt-2 text-center">
                                        <div class="overflow">
                                            <table class="table table-custom shadow-sm bg-white" id="example">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Beneficiary</th>
                                                        <th>amount</th>
                                                        <th>Annonymous Donation</th>
                                                        <th>Standing Order</th>
                                                        <th>Charity Note</th>
                                                        <th>Note</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @forelse ($donation as $data)
                                                        <tr>
                                                            <td><span style="display:none;">{{ $data->id }}</span>{{$data->created_at->format('d/m/Y')}}</td>
                                                            <td>{{$data->charity->name}}</td>
                                                            <td>£{{$data->amount}}</td>
                                                            <td>@if ($data->ano_donation == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif</td>
                                                            <td>@if ($data->standing_order == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif</td>
                                                            <td>{{$data->charitynote}}</td>
                                                            <td>{{$data->mynote}}</td>
                                                            <td>
                                                            @if($data->status == "0")
                                                                Pending
                                                            @elseif($data->status == "1")
                                                                Confirm
                                                            @elseif($data->status == "3")
                                                                Cancel
                                                            @endif
                                                            </td>

                                                        </tr>
                                                    @empty


                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>

</div>
@endsection
