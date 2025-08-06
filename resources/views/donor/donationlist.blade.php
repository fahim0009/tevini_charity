@extends('layouts.admin')

@section('content')

<style>
    .donation-checkbox {
        width: 20px;
        height: 20px;
    }
</style>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">New Donation List</div>
            </div>
            <div class="ermsg"></div>
        </section>
   
        <!-- Image loader -->
        <div id='loading' style='display:none ;'>
            <img src="{{ asset('assets/image/loader.gif') }}" id="loading-image" alt="Loading..." />
       </div>
     <!-- Image loader -->


        <section class="profile purchase-status">
            <div class="title-section">
                <div class="col-md-6">
                    <label for="charityFilter">Filter by Charity:</label>
                    <select id="charityFilter" class="form-control">
                        <option value="">Select Charity</option>
                        @foreach ($charities as $charity)
                        <option value="{{$charity->name}}">{{$charity->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>



         <section class="px-4"  id="contentContainer">
            <div class="row my-3">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">


                        <table class="table table-custom shadow-sm bg-white" id="example1">
                            <thead>
                                <tr>
                                    <th> Mark </th>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Beneficiary</th> <!-- This is Charity -->
                                    <th>Amount</th>
                                    <th>Annonymous Donation</th>
                                    <th>Charity Note</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($donation as $data)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="donation_id[]" value="{{ $data->id }}" class="donation-checkbox" data-charity="{{ $data->charity_id}}">
                                        </td>
                                        <td>{{ $data->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $data->user->name }}</td>
                                        <td>{{ trim($data->charity->name) }}</td>
                                        <td>£{{ $data->amount }}</td>
                                        <td>{{ $data->ano_donation == 'true' ? 'Yes' : 'No' }}</td>
                                        <td>{{ $data->charitynote }}</td>
                                        <td>{{ $data->mynote }}</td>
                                        <td>Pending</td>
                                        <td> 
                                            <select name="" id="" class="status form-control">
                                            <option value="0|{{$data->id}}" @if($data->status == "0")Selected @endif>Pending</option> 
                                            <option value="1|{{$data->id}}" @if($data->status == "1")Selected @endif>Complete</option> 
                                            <option value="3|{{$data->id}}" @if($data->status == "3")Selected @endif>Cancel</option> 
                                            </select> 
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9">
                                        <button class="text-decoration-none bg-success text-white py-1 px-3 rounded mb-1" id="completeBtn">Complete</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>






                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
    var title = 'Report: ';
    var data = 'Data: ';

    var table = $('#example1').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        responsive: true,
        columnDefs: [{ type: 'date', targets: [0] }],
        order: [[0, 'desc']],
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            { extend: 'copy' },
            { extend: 'excel', title: title },
            { 
                extend: 'print',
                exportOptions: { stripHtml: false },
                title: "<p style='text-align:center;'>" + data + "<br>" + title + "</p>",
                header: true,
                customize: function(win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ]
    });

    $('#charityFilter').on('change', function() {
        var charity = $(this).val();
        if (charity) {
            table.column(3).search('^' + charity + '$', true, false).draw();
        } else {
            table.column(3).search('', true, false).draw();
        }
    });

    // CSRF setup
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Status update handler
    var url = "{{ URL::to('/admin/donation-status') }}";

    $('.status').on('change', function() {
        $("#loading").show();
        var [status, did] = this.value.split("|");

        $.post(url, { status, did })
            .done(function(d) {
                if (d.status == 300) {
                    $(".ermsg").html(d.message);
                    setTimeout(() => location.reload(), 500);
                }
            })
            .always(() => $("#loading").hide())
            .fail(console.log);
    });

    // Checkbox handler

    $('#completeBtn').on('click', function() {
        var selected = [];
        var charity = [];
        $('.donation-checkbox:checked').each(function() {
            selected.push($(this).val());
            charity.push($(this).data('charity'));
        });

        let uniqueCharities = [...new Set(charity)];
        console.log(selected);

        if (selected.length > 0) {
            $("#loading").show();
            $.post("{{ URL::to('/admin/donation-complete') }}", { 
                donation_ids: selected,
                charity_ids: uniqueCharities
            })
                .done(function(d) {
                    console.log(d);
                    if (d.status == 300) {
                        $(".ermsg").html(d.message);
                        setTimeout(() => location.reload(), 500);
                    }
                })
                .always(() => $("#loading").hide())
                .fail(console.log);
        } else {
            alert('Please select at least one donation.');
        }
    });



});
</script>

@endsection
