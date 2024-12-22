<style>
 .innerMenu {
    margin: 9px 0;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    padding: 10px 14px;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    background-color: #ffffff;
  }
  @media (max-width: 650px) {
    .innerMenu {
      justify-content: center;
    }
  }
  .innerMenu a {
    margin-right: 20px;
    margin-top: 5px;
    margin-bottom: 5px;
    background: #43608b;
    padding: 3px 13px;
    border: 1px solid #03a9f4;
    border-radius: 15px;
    font-size: 15px;
    text-decoration: none;
    color: #fff;
    -webkit-transition: all 0.2s ease-in-out;
    transition: all 0.2s ease-in-out;
    text-transform: capitalize;
  }
  .innerMenu a:hover {
    transform: translateY(-2px);
    background: #69c5f2;
  }
  .actv{
    background: #69c5f2 !important;
  }
</style>


<section class="innerMenu">
    <a href="{{ route('donor.profile', $donor_id) }}" class="{{ (request()->is('admin/donor-profile*')) ? 'actv' : '' }}"> Profile </a>
    <a href="{{ route('donor.tranview', $donor_id) }}" class="{{ (request()->is('admin/donor-transaction*')) ? 'actv' : '' }}"> Transaction </a>
    <a href="{{ route('donor.donationrecord', $donor_id) }}" class="{{ (request()->is('admin/donation-record*')) ? 'actv' : '' }}"> Donation Record </a>
    <a href="{{ route('donor.standingorder', $donor_id) }}" class="{{ (request()->is('admin/standing-order*')) ? 'actv' : '' }}"> Standing Order </a>
    <a href="{{ route('donor.orderhistory', $donor_id) }}" class="{{ (request()->is('admin/donor-order-history*')) ? 'actv' : '' }}"> Order Record </a>
    <a href="{{ route('donor.vorder', $donor_id) }}" class="{{ (request()->is('admin/donor-voucher-order*')) ? 'actv' : '' }}"> Voucher </a>
    <a href="{{ route('donor.donation', $donor_id) }}" class="{{ (request()->is('admin/make-donation*')) ? 'actv' : '' }}"> Online Doantion </a>
    <a href="{{ route('donor.report', $donor_id) }}" class="{{ (request()->is('admin/donor-report*')) ? 'actv' : '' }}"> Report </a>
    <a href="{{ route('donor.topupreport', $donor_id) }}" class="{{ (request()->is('admin/donor-topup-report*')) ? 'actv' : '' }}">Topup Report </a>

    
    <a href="{{ route('pendingvoucher', $donor_id) }}" class="{{ (request()->is('admin/pending-voucher')) ? 'actv' : '' }}">Pending Voucher </a>
    <a href="{{ route('completevoucher', $donor_id) }}" class="{{ (request()->is('admin/complete-voucher')) ? 'actv' : '' }}">Complete Voucher </a>
    {{-- <a href=""> menu items </a>
    <a href=""> menu items </a>  --}}
    <input type="hidden" id="donor_id" value="{{ $donor_id }}">
</section>
