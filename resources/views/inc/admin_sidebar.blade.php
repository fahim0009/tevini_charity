<div class="leftSection wow fadeIn no-print" data-wow-delay=".25s" id='leftSidebar'>
    <div class="user-profile">
        <div class="close-dashboard-sidebar">
            <span class="iconify" onclick="foldSidebar();" data-icon="mdi:window-close"></span>
        </div>
        <img src="{{ asset('assets/image/logo/logo.png') }}" alt="">
    </div>
    <nav class="sidenav">
        <ul>
            <li class="nav-item {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}" id="admindashboard">
                <a href="{{ route('admin.dashboard') }}">
                    <span class="iconify" data-icon="clarity:dashboard-solid-badged"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/staff*')) ? 'active' : '' }}" id="admintransaction">
                <a href="{{ url('admin/staff') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Manage Admin
                </a>
            </li>
            <!--<li class="nav-item {{ (request()->is('admin/role*')) ? 'active' : '' }}" id="admintransaction">-->
            <!--    <a href="{{ url('admin/role') }}">-->
            <!--        <span class="iconify" data-icon="icon-park-outline:transaction"></span>-->
            <!--        Staff Role-->
            <!--    </a>-->
            <!--</li>-->
            <li class="nav-item {{ (request()->is('admin/transaction*')) ? 'active' : '' }}" id="">
                <a href="{{ route('transaction') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Latest Transactions
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/donor*')) ? 'active' : '' }}" id="">
                <a href="{{ route('donor') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Donor
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/campaign*')) ? 'active' : '' }}" id="">
                <a href="{{ route('campaign') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Campaign
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/charity*')) ? 'active' : '' }}" id="">
                <a href="{{ route('charitylist') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Charities
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/process-voucher*')) ? 'active' : '' }}" id="">
                <a href="{{ route('processvoucher') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Process Vouchers
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/complete-voucher*')) ? 'active' : '' }}" id="">
                <a href="{{ route('completevoucher') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Complete Vouchers
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/pending-voucher*')) ? 'active' : '' }}" id="">
                <a href="{{ route('pendingvoucher') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Pending Vouchers
                </a>
            </li>        

            <li class="nav-item {{ (request()->is('admin/order*')) ? 'active' : '' }}">
                <a href="#">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    Voucher Order
                </a>
                <ul class="sub-item">
                    <li class="{{ (request()->is('admin/order/new')) ? 'active' : '' }}">
                        <a href="{{ route('neworder') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            New Order list
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('completeorder') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            Complete order list
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cancelorder') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            Cancel order list
                        </a>
                    </li>
                </ul>
            </li>



            {{-- <li class="nav-item {{ (request()->is('admin/donor*')) ? 'active' : '' }}">
                <a href="#">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    Donor List
                </a>
                <ul class="sub-item">
                    <li>
                        <a href="{{ route('adddonor') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            Add Donor
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('donor') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            Donor List
                        </a>
                    </li>
                </ul>
            </li> --}}

            <li class="nav-item {{ (request()->is('admin/donationlist*')) ? 'active' : '' }}">
                <a href="#">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    Online Donation
                </a>
                <ul class="sub-item">
                    <li>
                        <a href="{{ route('donationlist') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            New Donation
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('donationstanding') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            Standing order
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('donationrecord') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            Donation record
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item {{ (request()->is('admin/commission*')) ? 'active' : '' }}" id="admintransaction">
                <a href="{{ route('commission') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Commission
                </a>
            </li>



            <li class="nav-item {{ (request()->is('admin/voucher-book*')) ? 'active' : '' }}" id="admintransaction">
                <a href="{{ route('voucherbooks') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Voucher Books Stock
                </a>
            </li>
            <li class="nav-item {{ (request()->is('admin/remittance*')) ? 'active' : '' }}" id="admintransaction">
                <a href="{{ route('remittance') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Remittance Reports
                </a>
            </li>
            {{-- <li class="nav-item " id="admintransaction">
                <a href="{{ route('processvoucher') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    News
                </a>
            </li>
            <li class="nav-item " id="admintransaction">
                <a href="{{ route('processvoucher') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    FAQ
                </a>
            </li> --}}

            <!--<li class="nav-item {{ (request()->is('admin/settings*')) ? 'active' : '' }}" id="admintransaction">-->
            <!--    <a href="{{ route('admin.settings') }}">-->
            <!--        <span class="iconify" data-icon="icon-park-outline:transaction"></span>-->
            <!--        Settings-->
            <!--    </a>-->
            <!--</li>-->
            
            
            <li class="nav-item {{ (request()->is('admin/admin-contact-mail*')) ? 'active' : '' }}" id="admintransaction">
                <a href="{{ route('admin.contactmail') }}">
                    <span class="iconify" data-icon="icon-park-outline:transaction"></span>
                    Contact Mail
                </a>
            </li>

            <li class="nav-item">
                <a href="#">
                    <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                    About Us
                </a>
                <ul class="sub-item">
                    <li>
                        <a href="{{ route('about.help') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            What we help
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('aboutcontent.show') }}">
                            <span class="iconify" data-icon="fluent:contact-card-28-regular"></span>
                            About Content
                        </a>
                    </li>
                </ul>
            </li>


            {{-- <li class="nav-item " id="admindashboard">
                <a href="./utility.html">
                    <span class="iconify" data-icon="icomoon-free:profile"></span>
                    helper page
                </a>
            </li> --}}
            {{-- <li class="nav-item " id="admindashboard">
                <a href="./remitence-note.html">
                    <span class="iconify" data-icon="clarity:heart-solid"></span>
                    Remittance Note
                </a>
            </li> --}}
        </ul>
    </nav>
</div>
