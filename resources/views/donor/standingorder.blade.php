@extends('layouts.admin')

@section('content')
<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span> <div class="mx-2">Donor Details</div>
        </div>
    </section>
    @include('inc.user_menue')
          
    <section class="px-4">
        <div class="row  my-3">
    
            <div class="col-md-12">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                      <button class="nav-link active" id="transactionOut-tab" data-bs-toggle="tab" data-bs-target="#nav-transactionOut" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Current</button>
                      <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-transcationIn" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Previous</button>
    
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    {{-- Current order start  --}}
    
    
                    <div class="tab-pane fade show active" id="nav-transactionOut" role="tabpanel" aria-labelledby="nav-transactionOut">
                              <section class="px-4"  id="contentContainer">
                                <div class="row my-3">
                    
                                    <div class="col-md-12 mt-2 text-center">
                                        <div class="overflow">
                                            <table class="table table-custom shadow-sm bg-white">
                                                <thead>
                                                    <tr>
                                                        <th>Sl</th>
                                                        <th>Starting</th>
                                                        <th>Beneficiary</th>
                                                        <th>amount</th>
                                                        <th>Annonymous Donation</th>
                                                        <th>Standing Order</th>
                                                        <th>Charity Note</th>
                                                        <th>Note</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $n = 1;
                                                    ?>
                                                    @forelse ($donation as $data)
                                                        <tr>
                                                            <td>{{$n++}}</td>
                                                            <td>{{$data->starting}}</td>
                                                            <td>{{$data->charity->name}}</td>
                                                            <td>£{{$data->amount}}</td>
                                                            <td>@if ($data->ano_donation == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif</td>
                                                            <td>@if ($data->standing_order == "true")
                                                                Yes
                                                            @else
                                                                No
                                                            @endif</td>
                                                            <td>{{$data->charitynote}}</td>
                                                            <td>{{$data->mynote}}</td>
                                                            <td>Pending</td>
                                                           
                                                        </tr>
                                                    @empty
                                                    <div class="row">
                                                      <div class="col-md-6 mx-auto d-flex align-items-center justify-content-center">
                                                    <div class="orderInfo">
                                                      You currently have no Standing Orders.
                                                      <a class="btn  btn-info my-3 text-white">Setup Standing Order</a>
                                                    </div>                                                                         
                                                  </div>
                                                </div>
                                                @endforelse              
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>  
    
                    </div>
    
                    {{-- current order end  --}}
    
                    {{-- previous order start  --}}
                    <div class="tab-pane fade" id="nav-transcationIn" role="tabpanel" aria-labelledby="nav-profile-tab">
                     
                      <section class="px-4"  id="contentContainer">
                        <div class="row my-3">
            
                            <div class="col-md-12 mt-2 text-center">
                                <div class="overflow">
                                    <table class="table table-custom shadow-sm bg-white">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Starting</th>
                                                <th>Beneficiary</th>
                                                <th>amount</th>
                                                <th>Annonymous Donation</th>
                                                <th>Standing Order</th>
                                                <th>Charity Note</th>
                                                <th>Note</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $n = 1;
                                            ?>
                                            @forelse ($pdonation as $data)
                                                <tr>
                                                    <td>{{$n++}}</td>
                                                    <td>{{$data->starting}}</td>
                                                    <td>{{$data->charity->name}}</td>
                                                    <td>£{{$data->amount}}</td>
                                                    <td>@if ($data->ano_donation == "true")
                                                        Yes
                                                    @else
                                                        No
                                                    @endif</td>
                                                    <td>@if ($data->standing_order == "true")
                                                        Yes
                                                    @else
                                                        No
                                                    @endif</td>
                                                    <td>{{$data->charitynote}}</td>
                                                    <td>{{$data->mynote}}</td>
                                                    <td>Pending</td>
                                                   
                                                </tr>
                                            @empty
                                            <div class="row">
                                              <div class="col-md-6 mx-auto d-flex align-items-center justify-content-center">
                                                <div class="orderInfo">
                                                  You don't have previous Standing Orders.
                                                  <a class="btn  btn-info my-3 text-white">Setup Standing Order</a>
                                                </div>
                                              </div>
                                          </div>
                                            @endforelse
            
            
            
            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>  
                      
    
    
    
                    </div>
                    {{-- Previous order end  --}}
    
                  </div>
            </div>
        </div>
      </section>
</div>
@endsection
