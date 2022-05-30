<div class="topbar">
    <div class="fold" onclick='foldSidebar();'>
        <span class="iconify" data-icon="eva:menu-fill"></span>
    </div>
    <!-- <img src="images/logo.png" class="mobile-menu-logo"> -->
    <div class="right-element">
        <div class="dropdown show">
            <a class="btn  dropdown-toggle  profile-manage" href="#" role="button" id="dropdownMenuLink"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset('assets/user/images/profile.png') }}">
            </a>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="{{ route('user.profile') }}"><span class="iconify"
                        data-icon="carbon:user-avatar"></span> Profile</a>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="iconify"
                        data-icon="ion:log-out-outline"></span> Log Out
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </a>


            </div>
        </div>
    </div>

</div>

<div class="userStatus">
    <div class="items">
        <span>Account</span>
        <span>{{auth()->user()->accountno}}</span>
    </div>
    <div class="items">
        <span>Balance</span>
        <span> £{{auth()->user()->balance}}</span>
    </div>
    <div class="items">
        <span>As of</span>
        <span>{{date('d/m/Y')}}</span>
    </div>
</div>
