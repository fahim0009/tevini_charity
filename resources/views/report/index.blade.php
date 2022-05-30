@extends('layouts.admin')

@section('content')

<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="et:wallet"></span>
            <div class="mx-2">
                Reports
            </div>
        </div>
    </section>
    <section class="px-4">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card border-info border-4   border-end-0 border-top-0 border-bottom-0 p-4 shadow-sm d-flex flex-row align-items-center">
                    <div class="flex-fill">
                        <h6 class="text-info fw-bold">Donor Annual Report</h6>
                        <p class="  mb-1 text-muted">
                            All donations over last 5 years including start date and latest date. This
                            report
                            is only run on a monthly basis.</p>
                    </div>
                    <a href="" class=" text-decoration-none text-center px-3 btn btn-sm btn-secondary">View</a>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card border-info border-4   border-end-0 border-top-0 border-bottom-0 p-4 shadow-sm d-flex flex-row align-items-center">
                    <div class="flex-fill">
                        <h6 class="text-info fw-bold">Standing Order Report</h6>
                        <p class="  mb-1 text-muted">
                            Table showing all active standing orders from donors with all the relevant details. This report is only run on a monthly basis in the first week of each month.</p>
                    </div>
                    <a href="" class=" text-decoration-none text-center px-3 btn btn-sm btn-secondary">View</a>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card border-info border-4   border-end-0 border-top-0 border-bottom-0 p-4 shadow-sm d-flex flex-row align-items-center">
                    <div class="flex-fill">
                        <h6 class="text-info fw-bold">Remittance Note Report</h6>
                        <p class="  mb-1 text-muted">
                            All remittance notes in the last 24 months with date, value and number of donations. You can click on a remittance number to see the donations in that remittance note. This report is only run on a daily basis.</p>
                    </div>
                    <a href="" class=" text-decoration-none text-center px-3 btn btn-sm btn-secondary">View</a>
                </div>
            </div>
        </div>
    </section>
</div>


@endsection
