@extends('layouts.admin')
@section('content')
@php
use Illuminate\Support\Carbon;
@endphp
@include('inc.user_menue')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">View All Vouchers</div>
        </div>
    </section>
  <section class="">
    <div class="row  my-3 mx-0 ">
        <div class="col-md-12 ">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">

                  <button class="nav-link active" id="nav-report-tab" data-bs-toggle="tab" data-bs-target="#nav-reportTab" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Report</button>

                </div>
              </nav>
              <div class="tab-content" id="nav-tabContent">
                
                
                <div class="tab-pane fade show active" id="nav-reportTab" role="tabpanel" aria-labelledby="nav-report-tab">
                    <div class="row my-2">
                        
                        <div class="col-md-12 mt-2 text-center">
                            <div class="overflow">
                                <table class="table table-custom shadow-sm bg-white">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Date</th>
                                            <th>Report </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($reports as $key => $report)

                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $report->created_at }}</td>
                                            <td><a class="text-white btn-theme ml-1" href="{{route('instreport', $report->batch_id)}}" style="text-decoration: none">Report</a></td>
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
    </div>
  </section>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        var title = 'Report: ';
        var data = 'Data: ';


        // datatable common
        $('#reportDT').DataTable({
            pageLength: 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            responsive: true,
            columnDefs: [ { type: 'date', 'targets': [0] } ],
            order: [[ 0, 'desc' ]],
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {extend: 'copy'},
                {extend: 'excel', title: title},
                // {extend: 'pdfHtml5',
                // title: 'Report',
                // orientation : 'portrait',
                //     header:true,
                //     customize: function ( doc ) {
                //         doc.content.splice(0, 1, {
                //                 text: [

                //                            { text: data+'\n',bold:true,fontSize:12 },
                //                            { text: title+'\n',bold:true,fontSize:15 }

                //                 ],
                //                 margin: [0, 0, 0, 12],
                //                 alignment: 'center'
                //             });
                //         doc.defaultStyle.alignment = 'center'
                //     }
                // },
                {extend: 'print',
                exportOptions: {
                stripHtml: false
            },
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
            ]
        });
    });
</script>
@endsection
