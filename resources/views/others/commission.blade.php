@extends('layouts.admin')

@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
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
        pageLength: 50,

        // ⭐ Add Buttons
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5'
            },
            {
                extend: 'csvHtml5'
            },
            {
                extend: 'excelHtml5'
            },
            {
                extend: 'pdfHtml5',
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
            {
                extend: 'print'
            }
        ]
    });

});
</script>


@endsection
