@extends('layouts.admin')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp

<style>
    /* Main container for the top controls */
    .top-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 10px;
        background: #f8f9fa; /* Light grey background for a clean look */
        border-radius: 8px;
    }

    /* Pull Length to the left */
    .dataTables_length {
        flex: 1;
        text-align: left;
    }

    /* Center the Buttons */
    .dt-buttons {
        flex: 1;
        text-align: center;
        display: flex;
        justify-content: center;
        gap: 5px;
    }

    /* Pull Search to the right */
    .dataTables_filter {
        flex: 1;
        text-align: right;
    }

    /* Style improvements for the table header */
    .table-custom thead {
        background-color: #4d617e;
        color: white;
    }
    
    /* Responsive adjustment */
    @media (max-width: 768px) {
        .top-wrapper {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>

<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Commissions</div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="commissionTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Donor Name</th>
                                            <th>Commission</th>
                                            </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
  </section>
</div>
@endsection

@section('script')

<script>

$(function () {
    $('#commissionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('commission') }}",
        columns: [
            { data: 'date', name: 'date' },
            { data: 'donor', name: 'donor' },
            { data: 'amount', name: 'amount' }
        ],
        lengthMenu: [[100, 25, 50, 100, -1], [100, 25, 50, 100, "All"]],
        
        dom: '<"top-wrapper"lBf>rtip', 

        buttons: [
            { extend: 'copyHtml5', className: 'btn btn-sm btn-secondary' },
            { extend: 'csvHtml5', className: 'btn btn-sm btn-secondary' },
            { extend: 'excelHtml5', className: 'btn btn-sm btn-secondary' },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-sm btn-secondary',
                title: "Commission Report",
                orientation: 'portrait',
                pageSize: 'A4',
                customize: function (doc) {
                    doc.styles.tableHeader = {
                        bold: true,
                        fontSize: 9,
                        fillColor: '#4d617e',
                        color: 'white',
                        alignment: 'center'
                    };
                    doc.defaultStyle.fontSize = 8;
                    doc.defaultStyle.alignment = 'center';
                    doc.pageMargins = [20, 40, 20, 30];
                }
            },
            { extend: 'print', className: 'btn btn-sm btn-secondary' }
        ]
    });
});


</script>


@endsection
