
            <div class="topbar no-print">
                <div class="fold" onclick='foldSidebar();'>
                    <span class="iconify" data-icon="eva:menu-fill"></span>
                </div>
                <h6 class="text-capitalize mb-0 border border-light shadow-sm text-white p-2 border-1 d-inline-block mx-auto rounded">
                    <small class="fw-bold">welcome to Tevini</small>
                </h6>
                <div class="right-element">
                    <div class="dropdown show">
                        <a class="btn  dropdown-toggle  profile-manage" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ asset('assets/admin/images/profile.png') }}">
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <!--<a class="dropdown-item" href="#"><span class="iconify"-->
                            <!--        data-icon="carbon:user-avatar"></span> Profile</a>-->
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
