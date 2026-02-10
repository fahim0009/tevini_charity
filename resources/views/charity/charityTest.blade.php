@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile">
        <div class="title-section d-flex align-items-center mb-4">
            <span class="iconify" data-icon="-park-outline:analysis" data-width="25"></span> 
            <h4 class="mx-2 mb-0">All Charity Financial Summary</h4>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Charity Name</th>
                            <th>Email</th>
                            <th class="text-end">In Amount (+)</th>
                            <th class="text-end">Out Amount (-)</th>
                            <th class="text-end">Tran Balance</th>
                            <th class="text-center">Charity Balance</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($charityData as $data)
                        <tr>
                            <td><strong>{{ $data['name'] }}</strong></td>
                            <td>{{ $data['email'] }}</td>
                            <td class="text-success text-end">{{ number_format($data['in_amt'], 2) }}</td>
                            <td class="text-danger text-end">{{ number_format($data['out_amt'], 2) }}</td>
                            <td class="text-end">
                                    {{ number_format($data['balance'], 2) }}
                            </td>
                            <td class="text-end">
                                <span class="badge {{ $data['balance'] == $data['cbalance'] ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format($data['cbalance'], 2)  }}
                                </span>
                            </td>
                            <td class="text-end">
                                    @if ($data['balance'] > $data['cbalance'])
                                        <span class="badge bg-danger">
                                            {{ number_format($data['balance'] - $data['cbalance'], 2)  }}
                                        </span>
                                    @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection