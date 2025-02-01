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
                                        <td>{{$item->user->name ?? " "}}</td>
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
