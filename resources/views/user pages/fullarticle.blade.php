@extends('layouts.app3')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Article</title>

    <meta content="Free HTML Templates" name="Searchs">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="/assets/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">   

      <!-- Add these links for Owl Carousel -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Add Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <!-- Customized Bootstrap Stylesheet -->
    <link href="\assets\css\style.css" rel="stylesheet">

    <style>
        .custom-container {
            max-width: 800px;
            margin-right: auto;
            margin-left: auto;
        }

    </style>
</head>
<body>
@section('content')


<main class="content px-3 py-2">
    <div  class="container mt-3 mb-3 d-flex justify-content-center align-items-center custom-container">
        <div class="card custom-card p-3" style="background-color: white; border: none;">
            <!-- First Section: Published Time -->
            <div class="card-header" style=" border: none;">
                <p class="text-muted mb-0" style="font-size: 14px; font-family: 'Open Sans';">
                    <span class="fw-bold">{{ \Carbon\Carbon::parse($article['created_at'])->diffForHumans() }} - {{ $article['category'] }}</span>
                </p>
            </div>

            <!-- Second Section: Title -->
            <h1 class="mt-3" style="max-width: 800px; font-weight: 400; font-family: Montserrat;">{{ $article['title'] }}</h1>


            <!-- Third Section: User Info -->
            <div class="card-header d-flex align-items-center" style=" border: none;">
                <img src="{{ $user['profile_picture'] }}" alt="Journalist Avatar" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                <div class="ms-3 mt-3">
                    @if (isset($user['firstName']))
                        <h6 class="mb-3 ml-2" style="font-size: 14px; font-family: 'Open Sans';">{{ $user['firstName'] }} {{ $user['lastName'] }}</h6>
                    @else
                        <p class="mb-0">Unknown Journalist</p>
                    @endif
                </div>
            </div>

            <!-- Fourth Section: Article Picture -->
                    @if ($article['image_path'])
                        <img src="{{ $article['image_path'] }}" alt="User" class="card-img-top border-0" style="max-height: 500px; max-width: 800px;">
                    @else
                        <span>No Picture</span>
                    @endif
            <!-- Fifth Section: Descriptions -->
            <div class="card-text mt-3" style="font-family: 'Open Sans'; max-width: 800px; color: #333; ">{!! $article['short_description'] !!}</div>
            <div class="card-text mt-3" style="font-family: 'Open Sans'; max-width: 800px; color: #333; ">{!! $article['full_description'] !!}</div>


        </div>
    </div>
</main>





  <!-- Back to Top -->
  <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="/assets/lib/easing/easing.min.js"></script>
<script src="/assets/lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Contact Javascript File -->
<script src="/assets/mail/jqBootstrapValidation.min.js"></script>
<script src="/assets/mail/contact.js"></script>

<!-- Template Javascript -->
<script src="/assets/js/main.js"></script>


<script>

            // JavaScript to Toggle Email and Logout Button Visibility
    document.getElementById('emailCircle').onclick = function() {
        var userDetails = document.getElementById('userDetails');
        if (userDetails.style.display === 'none') {
            userDetails.style.display = 'block';
        } else {
            userDetails.style.display = 'none';
        }
    }

    </script>
@endsection
</body>
</html>