@extends('frontend.layouts.user')

@section('content')

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Standing orders
            </div>
        </div>
    </div>
    <div class="row my-4">
        <div class="col-lg-12">
            <a href="{{route('user.makedonation')}}" class="btn-theme bg-primary text-white">Set up a standing order</a>
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-12">
            <div class="data-container">
                <table class="table table-theme mt-4">
                    <thead>
                        <tr>
                            <th scope="col">Sl</th>
                            <th scope="col">Starting</th>
                            <th scope="col">Beneficiary</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Annonymous Donation</th>
                            <th scope="col">Standing Order</th>
                            {{-- <th scope="col">Charity Note</th>
                            <th scope="col">Note</th> --}}
                            <th scope="col">Status</th>
                            <th scope="col">Activation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $n = 1;
                        ?>
                            @forelse ($donation as $data)
                                <tr>
                                    <td class="fs-16 txt-secondary">{{$n++}}</td>
                                    <td class="fs-16 txt-secondary">{{$data->starting}}</td>
                                    <td class="fs-16 txt-secondary">{{$data->charity->name}}</td>
                                    <td class="fs-16 txt-secondary">£{{$data->amount}}</td>
                                    <td class="fs-16 txt-secondary">@if ($data->ano_donation == "true")
                                        Yes
                                    @else
                                        No
                                    @endif</td>
                                    <td class="fs-16 txt-secondary">@if ($data->standing_order == "true")
                                        Yes
                                    @else
                                        No
                                    @endif</td>
                                    {{-- <td class="fs-16 txt-secondary">{{$data->charitynote}}</td>
                                    <td class="fs-16 txt-secondary">{{$data->mynote}}</td> --}}
                                    <td class="fs-16 txt-secondary">Pending</td>
                                    <td class="fs-16 txt-secondary">
    
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="flexSwitchCheckChecked"  data-id="{{$data->id}}" {{ $data->status ? 'checked' : '' }}>
                                            <span class="flip-indecator" data-toggle-on="Active" data-toggle-off="Inactive"></span>
                                        </div>
        
                                    </td>
                                    
                                </tr>
                            @empty
                            <div class="row">
                                <div class="col-md-6 mx-auto d-flex align-items-center justify-content-center">
                                    <div class="orderInfo">
                                        You currently have no Standing Orders.
                                        <button class="btn-theme bg-primary text-white">Set up a standing order</button>
                                    </div>                                                                         
                                </div>
                            </div>
                        @endforelse  


                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- content area -->
{{-- <div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Coming Soon ...
            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#standingorder").addClass('active');
    });
</script>
<script type="text/javascript">
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })

    function removeRow(event) {
            event.target.parentElement.parentElement.remove();
    }
</script>

<script>
    $(function() {
      $('.form-check-input').change(function() {
        // var url = "{{URL::to('/user/active-donation-details')}}";
          var status = $(this).prop('checked') == true ? 1 : 0;
          var id = $(this).data('id');
           console.log(status);
          $.ajax({
              type: "GET",
              dataType: "json",
              url: url,
              data: {'status': status, 'id': id},
              success: function(d){
                    if (d.status == 303) {
                        $(".stsermsg").html(d.message);
                    }else if(d.status == 300){
                        $(".stsermsg").html(d.message);
                        window.setTimeout(function(){location.reload()},2000)
                    }
                },
                error: function (d) {
                    console.log(d);
                }
          });
      })
    })
</script>

@endsection
