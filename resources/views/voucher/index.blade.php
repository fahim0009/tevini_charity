@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Voucher Stock</div>
            </div>
        </section>


        <section class="px-4"  id="contentContainer">
            <div class="row my-3">

                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-custom shadow-sm bg-white">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Stock</th>
                                    <th>Add Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $n = 1;
                                ?>
                                @forelse ($voucher as $data)
                                    <tr>
                                        <td>{{$n++}}</td>
                                        <td>{{$data->type}} @if($data->note)({{ $data->note }}) @endif</td>
                                        <td>£{{$data->amount}}</td>
                                        <td>{{$data->stock}}</td>
                                        <td>
                                            <button type="button" voucher-id="{{$data->id}}" class="btn btn-primary acc" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                add
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse




                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>


<!-- Button trigger modal -->


  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Stock </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="ermsg"></div>
            <div class="mb-3">
                <label for="stockno" class="form-label">Stock</label>
                <input type="text" class="form-control" id="stockno">
                <input type="hidden" class="form-control" value="" id="stockid">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" id="addBtn" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal End -->


@endsection

@section('script')

<script>
    $(document).ready(function () {
        //header for csrf-token is must in laravel
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        //add stock to voucher
        $(".acc").click(function(){
		var voucherid = $(this).attr("voucher-id");
        $('#stockid').val(voucherid);
	    });

        var url = "{{URL::to('/admin/add-stock')}}";
        $("#addBtn").click(function(){
        var stockId= $("#stockid").val();
        var stockno= $("#stockno").val();
        console.log(stockId);
        $.ajax({
            url: url,
            method: "POST",
            data: {stockId:stockId,stockno:stockno},
            success: function (d) {
                if (d.status == 303) {
                    $(".ermsg").html(d.message);
                }else if(d.status == 300){
                    $(".ermsg").html(d.message);
                    location.reload();
                }
            },
            error: function (d) {
                console.log(d);
            }
        });

            });

        //add stock to voucher END





    });





</script>
@endsection
