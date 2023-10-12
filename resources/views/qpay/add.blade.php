@extends('layouts.admin')

@section('content')


<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Qpay Balance Add </div>
            </div>
        </section>


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('qpaybalance.store') }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Balance</label>
                               <input type="number" name="balance" id="balance" class="form-control" value="" step="any">
                        </div>
                        
                    </div>
                    <div class="col-md-6  my-4  bg-white">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-theme mt-2 text-white">Update</button>
                    </div>
                    </form>
            </div>
        </section>



    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function () {


        





    });





</script>
@endsection
