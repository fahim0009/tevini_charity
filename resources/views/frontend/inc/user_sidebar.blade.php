<div class="sidebar " id="sidebar">
    <div class="brand">
        <img src="{{ asset('assets/user/images/logo.svg') }}" width="114px" class="mx-auto" alt="logo">
    </div>
    <ul class="navigation">

        <li><a href="{{ route('user.dashboard') }}" class="{{ (request()->is('user/dashboard*')) ? 'nav-link current' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('user.makedonation') }}" class="{{ (request()->is('user/make-donation*')) ? 'nav-link current' : '' }}">Make a donation</a></li>
        <li><a href="{{ route('user.donationrecord') }}" class="{{ (request()->is('user/donation-record*')) ? 'nav-link current' : '' }}">Donation records</a></li>
        <li><a href="{{ route('user.standingrecord') }}" class="{{ (request()->is('user/standing-order-record*')) ? 'nav-link current' : '' }}">Standing order records</a></li>
        <li><a href="{{ route('user.orderbook') }}" class="{{ (request()->is('user/order-voucher-book*')) ? 'nav-link current' : '' }}">Order voucher books</a></li>
        <li><a href="{{ route('user.orderhistory') }}" class="{{ (request()->is('user/order-history*')) ? 'nav-link current' : '' }}">Order Record</a></li>
        <li><a href="{{ route('user.process_voucher') }}" class="{{ (request()->is('user/process-voucher*')) ? 'nav-link current' : '' }}">Waiting vouchers</a></li>
        <li><a href="{{ route('userCardService')}}" class="{{ (request()->is('user/card-service*')) ? 'nav-link current' : '' }}">Tevini card</a></li>
        <li><a href="{{ route('user.transaction') }}" class="{{ (request()->is('user/transaction-view*')) ? 'nav-link current' : '' }}">View transactions</a></li>
        <li><a href="{{ route('user.donationcal') }}" class="{{ (request()->is('user/donation-calculation*')) ? 'nav-link current' : '' }}">Maaser calculator</a></li>
        <li><a href="{{ route('user.contact') }}" class="{{ (request()->is('user/contact*')) ? 'nav-link current' : '' }}">Contact/Top-Up</a></li>
        
        {{-- <li><a href="{{ route('userCardService') }}" class="{{ (request()->is('user/card-service*')) ? 'nav-link current' : '' }}">Card Service</a></li> --}}
    </ul>
    <div class="bottom-part">
        <a href="{{ route('user.orderbook') }}" class="btn-theme bg-secondary">Order voucher books</a>
        <a href="{{ route('user.makedonation')}}" class="btn-theme bg-primary">Make a donation</a>
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
