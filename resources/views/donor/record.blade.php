@extends('layouts.admin')
@section('content')
<div class="rightSection">
    <div class="dashboard-content">
        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Donation Records </div>
            </div>
        </section>
        @if(session()->has('message'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('message') }}</div>
            </div>
        </section>
        @endif
        @if(session()->has('error'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
            </div>
        </section>
        @endif

        <section class="px-4"  id="contentContainer">
            <div class="row my-3">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                    <div class="table-responsive">

                        <table class="table table-custom shadow-sm bg-white" id="donationTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Beneficiary</th>
                                    <th>Amount</th>
                                    <th>Anonymous Donation</th>
                                    <th>Standing Order</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


@endsection

@section('script')

<script>
$(function () {

    $('#donationTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('donationrecord') }}",
        columns: [
            { data: 'date', name: 'date' },
            { data: 'donor', name: 'donor' },
            { data: 'beneficiary', name: 'beneficiary' },
            { data: 'amount', name: 'amount' },
            { data: 'anonymous', name: 'anonymous' },
            { data: 'standing', name: 'standing' },
            { data: 'mynote', name: 'mynote' },
            { data: 'status_label', name: 'status_label', orderable:false, searchable:false }
        ],

        pageLength: 50,

        // ⭐ Add Buttons
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                title: "Donation Record Report",
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
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
                extend: 'print',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });

});
</script>


@endsection
