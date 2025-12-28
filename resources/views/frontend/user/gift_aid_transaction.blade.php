@extends('frontend.layouts.user')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Transactions
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-12">
            <ul class="nav nav-tabs mt-4" id="myTab" role="tablist">
                <li class="nav-item " role="presentation">
                    <button class="nav-link active" id="gift-tab" data-bs-toggle="tab" data-bs-target="#gift"
                        type="button" role="tab" aria-controls="gift" aria-selected="true">Gift Aid</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                {{-- gift Transaction  --}}
                <div class="tab-pane fade show mt-4 active" id="gift" role="tabpanel" aria-labelledby="gift-tab">

                    <div class="data-container">
                        <table class="table table-theme mt-4" id="example3">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction Id</th>
                                    <th>Donor Name</th>
                                    <th>Source</th>
                                    <th>Gift Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($giftAid as $gift)
                                @php
                                    $amount = $gift->amount * .25;
                                @endphp
                                <tr>
                                    <td>
                                        <span style="display:none;">{{ $gift->id }}</span>
                                        @if ($gift->date)
                                        {{Carbon::parse($gift->date)->format('d/m/Y')}}
                                        @else
                                        {{Carbon::parse($gift->created_at)->format('d/m/Y')}}
                                        @endif
                                    </td>
                                    <td>{{ $gift->t_id }}</td>
                                    <td>{{$gift->user->name ?? ""}}</td>
                                    <td>{{ $gift->source}}</td>
                                    <td>
                                        @if ($gift->clear_gift == 1)

                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#DC3545" class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.147-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z"/>
                                            </svg>
                                            
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#18988B" class="bi bi-arrow-up-circle" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                                                <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5A.5.5 0 0 0 8 12z"/>
                                            </svg>
                                            
                                        @endif

                                        £{{ number_format($amount, 2)}}
                                    </td>
                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('script')

<script>

$(document).ready(function() {

    var title = 'Report: ';
    var data = 'Data: ';


    var table = $('#allTransactionTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('user.donor.alltransaction') }}",
            data: function (d) {
                d.fromDate = $('#fromDate').val();
                d.toDate = $('#toDate').val();
            }
        },
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'description', name: 'description' },
            { data: 'donation_by', name: 'donation_by', defaultContent: '' },
            { data: 'amount', name: 'amount' },
            { data: 'note', name: 'note', defaultContent: '' },
            { data: 'reference', name: 'reference' },
            { data: 'calculated_balance', name: 'calculated_balance' }
        ],
        // Disable default ordering so it uses the order sent by the Controller
        "ordering": false, 
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'copy'},
            {extend: 'excel', title: title},
            {extend: 'pdfHtml5',
            title: 'Report',
            orientation : 'portrait',
                header:true,
                customize: function ( doc ) {
                    doc.content.splice(0, 1, {
                            text: [

                                    { text: data+'\n',bold:true,fontSize:12 },
                                    { text: title+'\n',bold:true,fontSize:15 }

                            ],
                            margin: [0, 0, 0, 12],
                            alignment: 'center'
                        });
                    doc.defaultStyle.alignment = 'center'
                }
            },
            {extend: 'print',
            title: "<p style='text-align:center;'>"+data+"<br>"+title+"</p>",
            header:true,
                customize: function (win){
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', 'inherit');
            }
            }
        ],



        drawCallback: function(settings) {
            var api = this.api();
            var lastRow = api.row(':last').data();
            if(lastRow) {
                $('#prev-balance').html(lastRow.calculated_balance);
            }

            // THIS IS THE FIX FOR MODALS:
            // Every time the table draws, move the modals to the body
            // and initialize them if necessary.
            $('body').append($('.modal')); 
        }
    });

    $('#filterButton').on('click', function(e) {
        e.preventDefault();
        table.ajax.reload(); 
    });
});










</script>
    
@endsection
