@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Campaign Donor List </div>
            </div>
        </section>

        @if ($id)
        <section class="px-4 profile purchase-status">
            @if (session('message'))
                <div class="alert alert-info" id="message">
                    {{ session('message') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" id="errMessage">
                    {{ session('error') }}
                </div>
            @endif
            @php
                $campaign  = \App\Models\Campaign::where('id', $id)->first();
                $charity = \App\Models\Charity::where('id', $campaign->charity_id)->first();
            @endphp
            <form action="{{route('campaignReport')}}" method="POST">
                @csrf
                <div class="row my-3 mx-3">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" value="{{$charity->email}}" id="email" name="email" required>
                                <input type="hidden" name="charityid" value="{{$charity->id}}">
                                <input type="hidden" name="campaignid" value="{{$id}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Send Email</button>
                    </div>
                </div>
            </form>
        </section>
        @endif
        

        <section class="px-4"  id="contentContainer">
            <div class="row my-3">
            <div class="ermsg"></div>
            <div class="row  my-3 mx-0 ">
                <div class="col-md-12 mt-2 text-center">
                    <div class="overflow">
                        <table class="table table-donor shadow-sm bg-white" id="example">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Donor</th>
                                    <th>Charity</th>
                                    <th>Campaign</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $key => $item)
                                    <tr>
                                        <td>{{$item->created_at->format('d/m/Y') }}</td>
                                        <td>{{$item->user->name ?? " "}} {{$item->user->surname ?? " "}}</td>
                                        <td>{{$item->campaign->charity->name ?? " "}}</td>
                                        @if (isset($item->campaign_id))
                                        <td>{{$item->campaign->campaign_title ?? " "}}</td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td>{{$item->amount}}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Total Amount</td>
                                    <td><b>{{ number_format($data->sum('amount'), 2) }}</b></td>
                                
                                </tr>
                            </tfoot>
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

window.onload = (event) => {
   let k = document.getElementById("example_wrapper");
   k.classList.add('px-0');
};


    $(document).ready(function () {


            setTimeout(function() {
                $('#successMessage').fadeOut('fast');
                $('#errMessage').fadeOut('fast');
            }, 3000);

    });

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
    $('#charity_id').select2({
      width: '100%',
      placeholder: "Select an Option",
      allowClear: true
    });
  </script>
@endsection
