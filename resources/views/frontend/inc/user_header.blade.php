<div class="topBar position-relative">
    <div class="items d-flex justify-content-between align-items-center flex-wrap">
        <label for="" class="position-relative">
            <iconify-icon class="icon" icon="ic:baseline-search"></iconify-icon>
            <input type="text" class="inputSearch" placeholder="Search">
        </label>
        <div class="txt-theme fs-16">Account Number: <span class="fw-bold">{{auth()->user()->accountno ?? ''}}</span> </div>
    </div>
    <div class="items position-relative d-flex justify-content-end align-items-center">
        <div class="dropdown account">
            <div class="d-flex align-items-center  dropdown-toggle" type="button" id="dropdownMenuButton1"
                data-bs-toggle="dropdown" aria-expanded="false">
                <span class="txt-theme fw-bold fs-16 me-2">{{auth()->user()->name}}</span>
                <iconify-icon class="fs-2" icon="mdi:user-circle-outline"></iconify-icon>
            </div>
            <ul class="dropdown-menu  " aria-labelledby="dropdownMenuButton1">
                <li><a class="dropdown-item" href="{{ route('user.profile') }}">My Profile</a></li>
                {{-- <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li> --}}
            </ul>
        </div>
    </div>
</div>
