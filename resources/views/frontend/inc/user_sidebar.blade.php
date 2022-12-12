<div class="leftSection wow fadeIn" data-wow-delay=".25s" id='leftSidebar'>
    <div class="user-profile">
        <div class="close-dashboard-sidebar">
            <span class="iconify" onclick="foldSidebar();" data-icon="mdi:window-close"></span>
        </div>
        <img src="{{ asset('assets/image/logo/logo.png') }}" width="160px" alt="">
    </div>
    <nav class="sidenav">
        <ul>
            <li class="nav-item {{ (request()->is('user/dashboard*')) ? 'active' : '' }}" id="userdashboard">
                <a href="{{ route('user.dashboard') }}">
                    <span class="iconify" data-icon="clarity:dashboard-solid-badged"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item {{ (request()->is('user/profile*')) ? 'active' : '' }}">
                <a href="{{ route('user.profile') }}">
                    <span class="iconify" data-icon="fontisto:wallet"></span>
                    Profiles
                </a>
            </li>

            <!-- only active user can see this -->
            @if(auth()->user()->status == "1")
            <li class="nav-item {{ (request()->is('user/transaction-view*')) ? 'active' : '' }}" id="transaction">
                <a href="{{ route('user.transaction') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    View transaction
                </a>
            </li>
            <li class="nav-item {{ (request()->is('user/make-donation*')) ? 'active' : '' }}">
                <a href="{{ route('user.makedonation') }}">
                    <span class="iconify" data-icon="clarity:heart-solid"></span>
                    Make a Donation
                </a>
            </li>
            <li class="nav-item {{ (request()->is('user/donation-calculation*')) ? 'active' : '' }}">
                <a href="{{ route('user.donationcal') }}">
                    <span class="iconify" data-icon="clarity:heart-solid"></span>
                    Donation Calculator
                </a>
            </li>
            <li class="nav-item {{ (request()->is('user/donation-record*')) ? 'active' : '' }}">
                <a href="{{ route('user.donationrecord') }}">
                    <span class="iconify" data-icon="icomoon-free:profile"></span>
                    Donation records
                </a>
            </li>
            <li class="nav-item {{ (request()->is('user/standing-order*')) ? 'active' : '' }}">
                <a href="{{ route('user.standingorder') }}">
                    <span class="iconify" data-icon="icomoon-free:profile"></span>
                    Standing Orders records
                </a>
            </li>
            <li class="nav-item {{ (request()->is('user/order-voucher-book*')) ? 'active' : '' }}">
                <a href="{{ route('user.orderbook') }}">
                    <span class="iconify" data-icon="fontisto:wallet"></span>
                    Order voucher book
                </a>
            </li>
            <li class="nav-item {{ (request()->is('user/order-history*')) ? 'active' : '' }}">
                <a href="{{ route('user.orderhistory') }}">
                    <span class="iconify" data-icon="fontisto:wallet"></span>
                    Order Record
                </a>
            </li>
            @endif

            <!--<li class="nav-item " id="userfaq">-->
            <!--    <a href="{{ route('user.faq') }}">-->
            <!--        <span class="iconify" data-icon="fluent:note-add-16-filled"></span>-->
            <!--        FAQ-->
            <!--    </a>-->
            <!--</li>-->
            <li class="nav-item " id="usercontact">
                <a href="{{ route('user.contact') }}">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    Contact
                </a>
            </li>


        </ul>
    </nav>
</div>
