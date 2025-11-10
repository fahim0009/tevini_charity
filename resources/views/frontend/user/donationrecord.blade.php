@extends('frontend.layouts.user')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icomoon-free:profile"></span> <div class="mx-2">Donation records </div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3">

        <div class="col-md-12">

                {{-- Current order start  --}}

                          <section class="px-4"  id="contentContainer">
                            <div class="row my-3">

                                <div class="col-md-12 mt-2 text-center shadow-sm">
                                    <div class=" overflow pt-3">
                                        <table class="table " id="example">
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
                                                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y')}}</td>
                                                        <td>{{$data->charity->name}}</td>
                                                        <td>£{{$data->amount}}</td>
                                                        <td>
                                                            @if ($data->ano_donation == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($data->standing_order == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif
                                                        </td>
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


                {{-- current order end  --}}


        </div>
    </div>
  </section>
</div>


@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#standingorder").addClass('active');
    });
</script>
@endsection
