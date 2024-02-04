

   <!-- Topbar Start -->
    <div class="container-fluid">
       
    <div class="row align-items-center py-2 px-lg-5  bg-gray bg-lg-transparent mt-0 mt-lg-2">
        <!-- Logo Section -->
        <div class="col-lg-4 d-none d-lg-block bg-black">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img src="/assets/img/logo.png" alt="Your Logo" class="img-fluid" style="max-height: 300px; max-width: 400px; margin-left: -70px">
            </a>
        </div>

        <!-- User Email and Logout -->
        <div class="col-lg-8 text-center text-lg-right">
            @if(session('user_email'))
                <div style="display: flex; justify-content: end; align-items: center; position: relative;">
                    <!-- Circle with First Letter of Email -->
                    <div id="emailCircle" style="width: 40px; height: 40px; background-color: #007bff; border-radius: 50%; color: white; text-align: center; line-height: 40px; cursor: pointer; margin-right: 10px;">
                        {{ strtoupper(substr(session('user_email'), 0, 1)) }}
                    </div>

                    <!-- Hidden Full Email and Logout Button -->
                    <div id="userDetails" style="display: none; position: absolute; top: 100%; right: 0; background-color: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); padding: 12px; z-index: 1;">
                        <p style="margin: 0;">{{ session('user_email') }}</p>
                        <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-block">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <p>No user logged in.</p>
            @endif
        </div>
    </div>

    </div>
    <!-- Topbar End -->

<!-- Navbar Start -->
<div class="container-fluid p-0 mb-5">
    <nav class="navbar navbar-expand-lg bg-white navbar-light py-2 py-lg-0 px-lg-5">
        <a href="{{ route('home') }}" class="navbar-brand d-block d-lg-none">
            <img src="/assets/img/logo.png" alt="Your Logo"  class="img-fluid" style="max-width: 270px; margin-left: -40px">
        </a>

        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between px-0 px-lg-3" id="navbarCollapse">
            <div class="navbar-nav mr-auto py-0">
                <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('user.categories') }} " class="nav-item nav-link {{ request()->routeIs('user.categories') ? 'active' : '' }}">Categories</a>
                <a href="{{ route('user.regions') }}" class="nav-item nav-link {{ request()->routeIs('user.regions') ? 'active' : '' }}">Regions</a>
            
            
                <a href="{{ route('user.contact') }}" class="nav-item nav-link {{ request()->routeIs('user.contact') ? 'active' : '' }}">Contact</a>
                <!-- <a href="{{ route('user.favorite') }}" class="nav-item nav-link {{ request()->routeIs('user.favorite') ? 'active' : '' }}">Favorite</a> -->
                <a href="#" class="nav-item nav-link" id="factFlashAiTrigger">FactFlash AI</a>


            </div>
            <div class="input-group ml-auto" style="width: 100%; max-width: 300px;">
                <input type="text" class="form-control" placeholder="Search">
                <div class="input-group-append">
                    <button class="input-group-text text-secondary"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div>
    </nav>
</div>
<!-- Navbar End -->

<!-- Dialog Box  -->
<div id="dialogBackdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; padding: 20px; border-radius: 5px; max-width: 500px; width: 100%; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); text-align: center;">
        <img src="/assets/img/logo.png" alt="Logo" style="max-width: 300px;"> <!-- Logo -->
        <h4 style="margin-top: 20px;">AI Functionality Coming Soon!</h4>
        <p>We're working hard to bring you the AI features. Stay tuned!</p>
        <button id="closeDialog" style="margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px;">OK</button>
    </div>
</div>


<script>
document.getElementById('factFlashAiTrigger').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the anchor link from redirecting
    var dialogBackdrop = document.getElementById('dialogBackdrop');
    dialogBackdrop.style.display = 'flex'; // This line will correctly apply 'display: flex' when needed
});

document.getElementById('dialogBackdrop').addEventListener('click', function(event) {
    if (event.target.id === 'dialogBackdrop' || event.target.id === 'closeDialog') {
        document.getElementById('dialogBackdrop').style.display = 'none';
    }
});

</script>

