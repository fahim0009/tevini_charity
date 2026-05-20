

<div class="d-none">


    
    <li class="nav-item {{ (request()->is('admin/productfee*')) ? 'active' : '' }}" id="admintransaction">
        <a href="{{ route('productfee') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Product fee
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/cardprofile*')) ? 'active' : '' }}" id="">
        <a href="{{ route('cardprofile') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Card Profile
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/spend-profile*')) ? 'active' : '' }}" id="">
        <a href="{{ route('spendprofile') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Spend profile
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/product/index*')) ? 'active' : '' }}" id="">
        <a href="{{ route('product.index') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Product
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/authorisation*')) ? 'active' : '' }}" id="">
        <a href="{{ route('authorisation') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Authorisation
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/settlement*')) ? 'active' : '' }}" id="">
        <a href="{{ route('settlement') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Settlement
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/expired*')) ? 'active' : '' }}" id="">
        <a href="{{ route('expired') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Expired
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/card-transaction*')) ? 'active' : '' }}" id="">
        <a href="{{ route('cardTransaction') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Card Transaction
        </a>
    </li>

    <li class="nav-item {{ (request()->is('admin/qpay-balance*')) ? 'active' : '' }}" id="">
        <a href="{{ route('qpaybalance') }}">
            <span class="iconify" data-icon="icon-park-outline:transaction"></span>
            Qpay Balance
        </a>
    </li>

    
</div>
