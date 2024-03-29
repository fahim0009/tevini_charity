<style>
    .dropdown-item {
        font-size: 26px;
    }
</style>  

<section class="siteHeader ">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg navbar-light py-0 ">
                <a class="navbar-brand" href="{{ route('homepage') }}">
                    <img src="{{ asset('assets/front/images/logo.svg') }}" class="py-2 img-fluid mx-auto" width="200px">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav mx-auto navCustom">

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('aboutus') }}">About </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tdf') }}">TDF </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('howitWorks') }}">how it works</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">Contact </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Open an account</a>
                        </li> --}}

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Open an account
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('register') }}">Register as Donor</a></li>
                                <li><a class="dropdown-item" href="{{ route('charity.register') }}">Register as Charity</a></li>
                            </ul>
                        </li>


                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Log in
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('login') }}">Log in as Donor</a></li>
                                <li><a class="dropdown-item" href="{{ route('charity_loginshow') }}">Log in as Charity</a></li>
                            </ul>
                        </li>

                    </ul>
                </div>

            </nav>

        </div>
    </div>
</section>



