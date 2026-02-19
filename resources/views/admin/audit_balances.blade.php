@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">All transaction </div>
            </div>
        </section>

        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
                <div class="container" style="padding: 20px; font-family: sans-serif;">
    <h2>Daily Transaction Audit Report</h2>
    <p>Checking if Daily "In" matches Daily "Out" (Payouts) per Charity.</p>

    <table class="table" cellpadding="10" style="border-collapse: collapse; width: 100%; text-align: left;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Date</th>
                <th>Charity</th>
                <th>Donations Received (In)</th>
                <th>Payout Processed (Out)</th>
                <th>Daily Difference</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report as $item)
            <tr style="{{ !$item['is_clean'] ? 'background-color: #fff3f3;' : '' }}">
                <td><strong>{{ $item['date'] }}</strong></td>
                <td>{{ $item['name'] }} (ID: {{ $item['charity_id'] }})</td>
                <td>{{ number_format($item['in'], 2) }}</td>
                <td>{{ number_format($item['out'], 2) }}</td>
                <td style="font-weight: bold; color: {{ $item['diff'] != 0 ? 'red' : 'green' }}">
                    {{ number_format($item['diff'], 2) }}
                </td>
                <td>
                    @if(!$item['is_clean'])
                        <span style="color: red;">❌ Misaligned</span>
                    @else
                        <span style="color: green;">✅ Balanced</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No activity found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
            </div>
        </section>
    </div>
</div>


@endsection

@section('script')
@endsection
