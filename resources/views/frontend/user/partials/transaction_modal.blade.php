@php
    use Illuminate\Support\Carbon;
@endphp

<div class="modal fade" id="tranModal{{$data->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #fdf3ee;">
            <div class="modal-header">
                <h1 class="modal-title fs-5 txt-secondary" id="exampleModalLabel">Transaction Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>{{ Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Transaction ID</td>
                        <td>:</td>
                        <td>{{ $data->t_id }}</td>
                    </tr>
                    <tr>
                        <td>Transaction Type</td>
                        <td>:</td>
                        <td>{{ $row->title ?? $data->title }}</td>
                    </tr>
                    @if($data->charity)
                    <tr>
                        <td>Charity Name</td>
                        <td>:</td>
                        <td>{{ $data->charity->name }}</td>
                    </tr>
                    @endif
                    @if ($data->donation_by)
                    <tr>
                        <td>Donate By</td>
                        <td>:</td>
                        <td>{{ $data->donation_by }}</td>
                    </tr>
                    @endif
                    @if ($data->provoucher)
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>{{ $data->provoucher->expired == "Yes" ? 'Expired' : 'Active' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Amount</td>
                        <td>:</td>
                        <td>£{{ number_format($data->amount, 2) }}</td>
                    </tr>
                    @if ($data->note)
                    <tr>
                        <td>Comment</td>
                        <td>:</td>
                        <td>{{ $data->note }}</td>
                    </tr>
                    @endif
                    @if ($data->barcode_image)
                    <tr>
                        <td colspan="3">
                            <img src="{{ asset($data->barcode_image) }}" alt="Barcode Image" class="img-fluid">
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>