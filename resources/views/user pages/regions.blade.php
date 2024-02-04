@extends('layouts.app3')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regions</title>

    <meta content="Free HTML Templates" name="Searchs">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="../assets/../assets/../assets/../assets/../assets/img/favicon.ico" rel="icon">

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
    <link href="..\assets\css\style.css" rel="stylesheet">
</head>
<body>
@section('content')





    <!-- Category News Slider Start -->
    <div class="container-fluid">
        <div class="container">
            <div class="row">

           
                @foreach ($articlesByRegion as $region => $articles)
                    <div class="col-lg-6 py-3">
                        <div class="bg-light py-2 px-4 mb-3">
                            <h3 class="m-0">{{ $region }}</h3>
                        </div>
                        <div class="owl-carousel owl-carousel-3 carousel-item-1 position-relative">
                            @foreach ($articles as $article)
                                <div class="position-relative " >
                                    <img class="img-fluid w-100 article-container" src="{{ $article['image_path'] }}" style="height: 350px; object-fit: cover;">
                                    <div class="overlay position-relative bg-light">
                                        <div class="mb-2" style="font-size: 13px;">
                                            <a href="">{{ $article['category'] }}</a>
                                            <span class="px-1">/</span>
                                            <span>{{ date('F d, Y', strtotime($article['created_at'])) }}</span>
                                        </div>
                                        <a class="h4 m-0" href="{{ route('user.full-article', ['articleId' => $article['id']]) }}">{{ $article['title'] }}</a>
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </div>
    
    <!-- region News Slider End -->


  <!-- Back to Top -->
  <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="../assets/../assets/lib/easing/easing.min.js"></script>
<script src="../assets/../assets/lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Contact Javascript File -->
<script src="../assets/../assets/mail/jqBootstrapValidation.min.js"></script>
<script src="../assets/../assets/mail/contact.js"></script>

<!-- Template Javascript -->
<script src="../assets/../assets/js/main.js"></script>


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