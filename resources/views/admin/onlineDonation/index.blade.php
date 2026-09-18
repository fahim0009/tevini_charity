@extends('layouts.admin')
@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> 
            <div class="mx-2">Online Donation From Guest Donor</div>
        </div>
    </section>
    
    <section class="px-4" id="contentContainer">
        <div class="row my-3">
            <div class="col-md-12 mt-2 text-center">
                <div class="overflow">
                    <table class="table table-custom shadow-sm bg-white" id="example">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Guest Donor</th>
                                <th>Beneficiary</th>
                                <th>Amount</th>
                                <th>Admin Charge</th>
                                <th>Payment Method</th>
                                <th>Standing Order</th>
                                <th>Charity Note</th>
                                <th>Donor Note</th>
                                <th>Contact Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($donations as $data)
                                <tr>
                                    <td>
                                        <span style="display:none;">{{ $data->id }}</span>
                                        {{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        {{ $data->guest_first_name }} {{ $data->guest_last_name }}
                                    </td>
                                    <td>{{ $data->charity->name ?? 'N/A' }}</td>
                                    <td>£{{ number_format($data->amount, 2) }}</td>
                                    <td>£{{ number_format($data->admin_charge, 2) }}</td>
                                    <td>
                                        <span class="text-capitalize">{{ $data->payment_method }}</span>
                                    </td>
                                    <td>
                                        @if ($data->standing_order == "true")
                                            Yes
                                        @else
                                            No
                                        @endif
                                    </td>
                                    <td>{{ $data->charitynote ?? '-' }}</td>
                                    <td>{{ $data->mynote ?? '-' }}</td>
                                    <td>
                                        <small><strong>Email:</strong> {{ $data->guest_email }}</small><br>
                                        <small><strong>Phone:</strong> {{ $data->guest_phone ?? '-' }}</small><br>
                                        <small><strong>Address:</strong> {{ $data->guest_address_1 }}, {{ $data->guest_town }}, {{ $data->guest_postcode }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        No online guest donations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection