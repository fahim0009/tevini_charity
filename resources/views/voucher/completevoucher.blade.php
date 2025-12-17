@extends('layouts.admin')

@section('content')

    
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Complete Voucher</div>
        </div>
    </section>
    
    @if (isset($donor_id))
        @include('inc.user_menue')
    @endif
  <section class="">
    <input type="hidden" id="donorid" value="{{$donorid}}">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">

                <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                    <div class="row my-2">
                        
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white" id="example3">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Completed Date</th>
                                            <th>Charity</th>
                                            <th>Donor</th>
                                            <th>Cheque No</th>
                                            <th>Note</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    </tbody>
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
    $(document).ready(function() {
        let id = $('#donorid').val();

        $('#example3').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('completevoucher') }}",
                type: "GET",
                data: function (d) {
                    d.id = id;
                }
            },
            pageLength: 100,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],

            // 🔥 enable button layout (same as previous)
            dom: '<"html5buttons"B>lTfgitp',

            buttons: [
                {
                    extend: 'copy',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'csv',
                    title: "Complete Voucher Report",
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'excel',
                    title: "Complete Voucher Report",
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'pdfHtml5',
                    title: "Complete Voucher Report",
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: { columns: ':visible' },
                    customize: function(doc) {
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 10,
                            fillColor: '#4d617e',
                            color: 'white',
                            alignment: 'center'
                        };
                        doc.defaultStyle.alignment = 'center';
                        doc.pageMargins = [20, 40, 20, 30];
                    }
                },
                {
                    extend: 'print',
                    title: "<h3 style='text-align:center;'>Complete Voucher Report</h3>",
                    exportOptions: { columns: ':visible' }
                }
            ],

            columns: [
                { data: 'created_at', name: 'created_at' },
                { data: 'completed_date', name: 'completed_date' },
                { data: 'charity', name: 'charity' },
                { data: 'user', name: 'user' },
                { data: 'cheque_no', name: 'cheque_no' },
                { data: 'note', name: 'note' },
                { data: 'amount', name: 'amount' }
            ]
        });
    });

</script>
@endsection
