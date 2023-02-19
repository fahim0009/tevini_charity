@extends('frontend.layouts.user')

@section('content')

<!-- content area -->
{{-- <div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Standing orders
            </div>
        </div>
    </div>
    <div class="row my-4">
        <div class="col-lg-12">
            <button class="btn-theme bg-primary text-white">Set up a standing order</button>
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
                            <th scope="col">amount</th>
                            <th scope="col">Annonymous Donation</th>
                            <th scope="col">Standing Order</th>
                            <th scope="col">Charity Note</th>
                            <th scope="col">Note</th>
                            <th scope="col">Status</th>
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
                                    <td class="fs-16 txt-secondary">{{$data->charitynote}}</td>
                                    <td class="fs-16 txt-secondary">{{$data->mynote}}</td>
                                    <td class="fs-16 txt-secondary">Pending</td>
                                    
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
</div> --}}

<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
                Coming Soon ...
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#standingorder").addClass('active');
    });
</script>
@endsection
