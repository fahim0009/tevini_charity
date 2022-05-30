@extends('layouts.admin')

@section('content')



<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Contact mail Edit </div>
            </div>
        </section>


        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form action="{{ route('contactmail.update', $mail->id) }}" method="POST" enctype="multipart/form-data" id="createThisForm">
                            @csrf
                        <div class="col my-3">
                                <label for="">Email</label>
                               <input type="text" name="name" id="name" placeholder="Title1" class="form-control" value="{{ $mail->name }}">
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
