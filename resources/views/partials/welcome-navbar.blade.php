    <!-- Topbar Start -->
    <div class="container-fluid">
       
    <div class="row align-items-center py-2 px-lg-5  bg-gray bg-lg-transparent mt-0 mt-lg-2">
        <!-- Logo Section -->
        <div class="col-lg-4 d-none d-lg-block bg-black">
            <a href="" class="navbar-brand">
                <img src="/assets/img/logo.png" alt="Your Logo" class="img-fluid" style="max-height: 300px; max-width: 400px; margin-left: -70px">
            </a>
            </div>
            <div class="col-lg-8 text-center text-lg-right">
                <a href="{{ route('login') }}" style="font-family: 'Georgia', serif;"><button class="btn btn-dark">Login</button></a>
                <a href="register" style="font-family: 'Georgia', serif;"><button class="btn btn-dark">Sign Up</button></a>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid p-0 mb-5">
        <nav class="navbar navbar-expand-lg bg-white navbar-light py-2 py-lg-0 px-lg-5">
            <a href="welcome" class="navbar-brand d-block d-lg-none ">
                <img src="/assets/img/logo.png" alt="Your Logo"  class="img-fluid" style="max-width: 270px; margin-left: -40px">
            </a>

            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between px-0 px-lg-3" id="navbarCollapse">
                <div class="navbar-nav mr-auto py-0">

                <a href="{{ route('welcome') }}" class="nav-item nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}">Home</a>

                <a href="{{ route('welcome.categories') }}" class="nav-item nav-link {{ request()->routeIs('welcome.categories') ? 'active' : '' }}">Categories</a>
            
                <a href="{{ route('welcome.contact') }}" class="nav-item nav-link {{ request()->routeIs('welcome.contact') ? 'active' : '' }}">Contact</a>
                </div>
                <div class="input-group ml-auto" style="width: 100%; max-width: 300px;">
                    <input type="text" class="form-control" placeholder="Search">
                    <div class="input-group-append">
                        <button class="input-group-text text-secondary"><i
                                class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar End -->