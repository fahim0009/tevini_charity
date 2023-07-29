@extends('layouts.admin')

@section('content')



<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

<div class="rightSection">

    <div class="dashboard-content">

        <section class="profile purchase-status">
            <div class="title-section">
                <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                <div class="mx-2">Authorisation Details</div>
            </div>
        </section>

        <section class="profile purchase-status">
            <div class="title-section">
                <a href="{{ url()->previous() }}" type="button" class="btn btn-info">Back</a>
            </div>
        </section>

        <section class="px-4"  id="addThisFormContainer">
            <div class="row my-3">

                    <div class="col-md-6  my-4 bg-white">
                        <form >
                            @csrf
                         <div class="col">
                           <p><strong>Utid: </strong> {{$data->Utid}} </p>
                        </div>
                         <div class="col">
                           <p><strong>messageID : </strong> {{$data->messageID}} </p>
                        </div>

                         <div class="col">
                            <p><strong>instCode : </strong> {{$data->instCode}} </p>
                        </div>
                         <div class="col">
                            <p><strong>txnType : </strong> {{$data->txnType}} </p>
                        </div>

                         <div class="col my-3">
                            <p><strong>msgType : </strong>  {{$data->msgType}}</p>
                        </div>

                        <div class="col">
                            <p><strong>tlogId : </strong> {{$data->tlogId}} </p>
                        </div>

                        <div class="col">
                            <p><strong>orgTlogID : </strong> {{$data->orgTlogID}} </p>
                        </div>

                        <div class="col">
                            <p><strong>timeout : </strong> {{$data->timeout}} </p>
                        </div>

                        <div class="col">
                            <p><strong>repeat : </strong> {{$data->repeat}} </p>
                        </div>
                        <div class="col">
                            <p><strong>cardID : </strong> {{$data->cardID}} </p>
                        </div>
                        <div class="col">
                            <p><strong>accNo : </strong> {{$data->accNo}} </p>
                        </div>
                        <div class="col">
                            <p><strong>curBill : </strong> {{$data->curBill}} </p>
                        </div>
                        <div class="col">
                            <p><strong>avlBal : </strong> {{$data->avlBal}} </p>
                        </div>
                        <div class="col">
                            <p><strong>blkAmt : </strong> {{$data->blkAmt}} </p>
                        </div>
                        <div class="col">
                            <p><strong>localDate : </strong> {{$data->localDate}} </p>
                        </div>
                        <div class="col">
                            <p><strong>localTime : </strong> {{$data->localTime}} </p>
                        </div>
                        <div class="col">
                            <p><strong>amtTxn : </strong> {{$data->amtTxn}} </p>
                        </div>
                        <div class="col">
                            <p><strong>curTxn : </strong> {{$data->curTxn}} </p>
                        </div>
                        <div class="col">
                            <p><strong>billAmt : </strong> {{$data->billAmt}} </p>
                        </div>
                        <div class="col">
                            <p><strong>billConvRate : </strong> {{$data->billConvRate}} </p>
                        </div>
                        <div class="col">
                            <p><strong>amtCom : </strong> {{$data->amtCom}} </p>
                        </div>
                        <div class="col">
                            <p><strong>amtPad : </strong> {{$data->amtPad}} </p>
                        </div>
                        <div class="col">
                            <p><strong>txnCode : </strong> {{$data->txnCode}} </p>
                        </div>
                        <div class="col">
                            <p><strong>termCode : </strong> {{$data->termCode}} </p>
                        </div>

                        
                        <div class="col">
                            <p><strong>crdAcptID : </strong> {{$data->crdAcptID}} </p>
                        </div>
                        <div class="col">
                            <p><strong>crdAcptLoc : </strong> {{$data->crdAcptLoc}} </p>
                        </div>
                        <div class="col">
                            <p><strong>MCC : </strong> {{$data->MCC}} </p>
                        </div>
                        <div class="col">
                            <p><strong>poschp : </strong> {{$data->poschp}} </p>
                        </div>
                        <div class="col">
                            <p><strong>poscdim : </strong> {{$data->poscdim}} </p>
                        </div>
                        <div class="col">
                            <p><strong>poscham : </strong> {{$data->poscham}} </p>
                        </div>
                        <div class="col">
                            <p><strong>poscp : </strong> {{$data->poscp}} </p>
                        </div>
                        <div class="col">
                            <p><strong>approvalCode : </strong> {{$data->approvalCode}} </p>
                        </div>
                        <div class="col">
                            <p><strong>sysDate : </strong> {{$data->sysDate}} </p>
                        </div>
                        <div class="col">
                            <p><strong>rev : </strong> {{$data->rev}} </p>
                        </div>
                        <div class="col">
                            <p><strong>orgItemId : </strong> {{$data->orgItemId}} </p>
                        </div>
                        <div class="col">
                            <p><strong>itemSrc : </strong> {{$data->itemSrc}} </p>
                        </div>
                        <div class="col">
                            <p><strong>amtFee : </strong> {{$data->amtFee}} </p>
                        </div>
                        <div class="col">
                            <p><strong>crdproduct : </strong> {{$data->crdproduct}} </p>
                        </div>
                        <div class="col">
                            <p><strong>ctxLocalDate : </strong> {{$data->ctxLocalDate}} </p>
                        </div>
                        <div class="col">
                            <p><strong>ctxLocalTime : </strong> {{$data->ctxLocalTime}} </p>
                        </div>
                        <div class="col">
                            <p><strong>aVSChkRs : </strong> {{$data->aVSChkRs}} </p>
                        </div>
                        <div class="col">
                            <p><strong>threeDSecChkRs : </strong> {{$data->threeDSecChkRs}} </p>
                        </div>
                        <div class="col">
                            <p><strong>actionCode : </strong> {{$data->actionCode}} </p>
                        </div>
                        <div class="col">
                            <p><strong>amtCashback : </strong> {{$data->amtCashback}} </p>
                        </div>
                        <div class="col">
                            <p><strong>trn : </strong> {{$data->trn}} </p>
                        </div>
                        <div class="col">
                            <p><strong>txnSubCode : </strong> {{$data->txnSubCode}} </p>
                        </div>

                    </div>
                    <div class="col-md-6  my-4  bg-white">
                    </div>

                    </form>
            </div>
        </section>

    </div>
</div>

@endsection

@section('script')

@endsection
