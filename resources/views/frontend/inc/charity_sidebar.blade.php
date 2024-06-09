<div class="sidebar " id="sidebar">
    <div class="brand">
        <img src="{{ asset('assets/user/images/logo.svg') }}" width="114px" class="mx-auto" alt="logo">
    </div>
    <ul class="navigation">
        <li><a href="{{ route('charityDashboard') }}" class="{{ (request()->is('charity/dashboard*')) ? 'nav-link current' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('tran_charity_dashboard') }}" class="{{ (request()->is('charity/charity-transaction*')) ? 'nav-link current' : '' }}">Transaction</a></li>
        <li><a href="{{ route('charity_link') }}" class="{{ (request()->is('charity/create-a-link*')) ? 'nav-link current' : '' }}">Link</a></li>

        
        <li><a href="{{ route('charity.processvoucher') }}" class="{{ (request()->is('charity/process-voucher*')) ? 'nav-link current' : '' }}">Process Voucher</a></li>
        
        <li><a href="{{ route('charity.pendingvoucher') }}" class="{{ (request()->is('charity/pending-voucher*')) ? 'nav-link current' : '' }}">Pending Voucher</a></li>


    </ul>
    <div class="bottom-part">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="mt-2 d-flex justify-content-center txt-theme fw-bold align-items-center">
            <iconify-icon icon="humbleicons:logout"></iconify-icon>
            &nbsp;Log out
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
    <div class="collapsable" onclick="collaps();">
        <iconify-icon class="icon" icon="octicon:sidebar-collapse-24"></iconify-icon>
    </div>
</div>
