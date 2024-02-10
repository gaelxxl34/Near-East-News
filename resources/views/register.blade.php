<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

      <!-- Custom CSS to override Bootstrap primary color -->
      <style>
        .btn-primary {
            background-color: red;
            border-color: red;
        }

        .btn-primary:hover {
            background-color: darkred;
            border-color: darkred;
        }

        .text-primary {
            color: red !important;
        }
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }

        /* Custom CSS to make text bolder */
        .navbar-brand h1, .footer-brand h3 {
            font-weight: 600; /* Adjust the weight as needed */
        }
        .navbar {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); /* Adjust as needed */
        }
        #intro {
        background-image: url(https://mdbootstrap.com/img/new/fluid/city/008.jpg);
        height: 100vh;
      }

    

      .navbar .nav-link {
        color: #fff !important;
      }
    </style>

</head>
<body>

@include('partials.topbar')

   
<!-- Registration Form -->
<div id="intro" class="bg-image shadow-2-strong">
    <div class="mask d-flex align-items-center h-100" style="background-color: rgba(0, 0, 0, 0.8);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-md-8">


                <div class="bg-white rounded-5 shadow-5-strong p-1"  style="font-family: 'Montserrat';">
                    <!-- Simple Text Header -->
                    <h2 class="text-center mb-0 ">Register</h2>

                    <form id="registrationForm" action="{{ route('register') }}" method="POST" class="bg-white rounded-5 shadow-5-strong p-5">
                        @csrf <!-- CSRF Token -->

                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control" required />
                            <label class="form-label" for="email">Email address</label>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <input type="password" id="password" name="password" class="form-control" required />
                            <label class="form-label" for="password">Password</label>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                        
                        @if ($errors->has('register_error'))
                            <p class="mt-3 text-danger">{{ $errors->first('register_error') }}</p>
                        @endif
                        <!-- Text for users who already have an account -->
                        <p class="mt-3 text-center">
                            Already have an account? <a href="{{ route('login') }}">Log in</a>
                        </p>
                    </form>
                    </div>
                </div>
            
            </div>
        </div>
    </div>
</div>
<!-- End Registration Form -->









        <!-- Footer Start -->
        @include('partials.footer')
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>




<!-- Firebase Integration -->
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/ui/6.0.1/firebase-ui-auth.js"></script>




<script>
    $(document).ready(function(){
        // When the user clicks on the button, scroll to the top of the document
        $('.back-to-top').click(function(event){
            event.preventDefault();
            $('html, body').animate({scrollTop: 0}, 'slow');
            return false;
        });
    });

</script>



</body>
</html>
