<div class="sidebar " id="sidebar">
    <div class="brand">
        <img src="{{ asset('assets/user/images/logo.svg') }}" width="114px" class="mx-auto" alt="logo">
    </div>
    <ul class="navigation">
        <li><a href="{{ route('user.dashboard') }}" class="{{ (request()->is('user/dashboard*')) ? 'nav-link current' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('user.makedonation') }}" class="{{ (request()->is('user/make-donation*')) ? 'nav-link current' : '' }}">Make a donation</a></li>
        <li><a href="{{ route('user.donationrecord') }}" class="{{ (request()->is('user/donation-record*')) ? 'nav-link current' : '' }}">Donation Record</a></li>
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
