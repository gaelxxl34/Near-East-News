@extends('layouts.app3')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Home</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="Searchs">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="assets/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">   

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets\css\style.css" rel="stylesheet">
    <style>

        .large-margin-bottom {
            margin-bottom: 80px; /* Adjust this value as needed */
        }

        .article-separator {
            display: block;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

    </style>
</head>

<body>
@section('content')




<div class="container-fluid">
        <div class="row">
            <!-- Left Column for Latest News -->
        
            <div class="col-md-4 border-right pl-md-5 ">
                <h4 class="mb-3" style="font-family: 'Montserrat';">Latest stories</h4>
                    <div class="latest-news mb-5" >
                        
                        <!-- News Item 1 -->
                        @php $articleNumber = 1; @endphp
                        @foreach ($articlesByCategory as $categoryName => $topnews)
                            @foreach ($topnews as $index => $topnew) <!-- Loop through articles in this category -->
                                <a href="{{ route('user.full-article', ['articleId' => $topnew['id']]) }}" class="text-decoration-none text-dark"> <!-- Update this href with your target URL -->
                                    <div class="d-flex mb-3">
                                        <img src="{{ $topnew['image_path'] }}" class="mr-3" style="width: 100px; height: 100px; object-fit: cover;">
                                        <div>
                                            <h6 style="font-family: 'Open Sans';">{{ $articleNumber }}. {{ $topnew['title'] }}</h6>
                                            <p>{{ \Carbon\Carbon::parse($topnew['created_at'])->diffForHumans() }} - {{ $topnew['category'] }}</p>
                                        </div>
                                    </div>
                                </a>
                                @php $articleNumber++; @endphp
                            @endforeach
                        @endforeach
                    
            
                

                <!-- Add more latest news items here -->
            </div>
                

<hr>
                <h4 class="mb-3" style="font-family: 'Montserrat';">Top 5 Highlights</h4>
                <div class="top-news">
                    @foreach ($highlight as $categoryName => $highlights)
                        @foreach ($highlights as $highlight) <!-- Loop through articles in this category -->
                            <a href="{{ route('user.full-article', ['articleId' => $highlight['id']]) }}" class="text-decoration-none text-dark article-separator"> <!-- Update this href with your target URL -->
                                <div>
                                    <h6 style="font-family: 'Open Sans';">{{ $highlight['title'] }}</h6>
                                    <p>{{ \Carbon\Carbon::parse($highlight['created_at'])->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    @endforeach
                    <!-- Add more top news items here -->
                </div>
        </div>

        <!-- Right Column for Articles -->
            <div class="col-md-8">
                <!-- Single Article -->
                @if (!empty($articles))
                @forelse ($articles as $article)
                <div class="article  large-margin-bottom">
                    <!-- Author Info -->
                    <div class="media">
                        <img src="{{ $article['profile_picture'] }}" class="mr-3 rounded-circle" alt="Author" style="width: 35px; height: 35px; object-fit: cover;">
                        <div class="media-body">


                        @if (isset($article['firstName']))
                                            <h6 class="mb-0" style="font-size: 14px; font-family: 'Open Sans';">{{ $article['firstName'] }} {{ $article['lastName'] }}</h6>
                                        @else
                                            <p class="mb-0">Unknown Journalist</p>
                                        @endif
                            <p class="text-muted" style="margin-top: 0; font-size: 14px">{{ \Carbon\Carbon::parse($article['created_at'])->diffForHumans() }} - {{ $article['category'] }}</p>
                        </div>

                    </div>

                    <!-- Article Title -->
                    <a href="{{ route('user.full-article', ['articleId' => $topnew['id']]) }}" class="text-decoration-none text-dark">
                    <h1 class="mt-3" style="max-width: 800px; font-weight: 400; font-family: 'Montserrat';">{{ $article['title'] }}</h1>


                    <!-- Article Image -->

                @if ($article['image_path'])
                        <img src="{{ $article['image_path'] }}" alt="User" class="card-img-top border-0" style="max-height: 500px; max-width: 800px;">
                    @else
                        <span>No Picture</span>
                    @endif
    </a>

                    <!-- Short Description -->
                    <div class="card-text mt-3" style="font-size: 20px; font-family: 'Open Sans'; max-width: 800px; color: #333;">{!! $article['short_description'] !!}</div>

                    <!-- Read More Link -->
                    <p><a href="{{ route('user.full-article', ['articleId' => $article['id']]) }}" class="text-primary">Go deeper ({{ $article['reading_time'] }} min. read) <span>&rarr;</span></a></p>
                </div>
                @empty
                    <div class="col">
                        <div class="container mt-3">
                                No Articles Found
                        </div>
                    </div>
                @endforelse

                    @elseif (isset($message))

                        <div class="alert alert-info" role="alert">
                            {{ $message }}
                        </div>
                    @else
                        
                        <div class="alert alert-info" role="alert">
                            No articles are currently available.
                        </div>
                    @endif
                <!-- Add more articles here -->
            </div>

        </div>
    </div>




    <!-- Back to Top -->
    <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="assets/lib/easing/easing.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="assets/mail/jqBootstrapValidation.min.js"></script>
    <script src="assets/mail/contact.js"></script>

    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>
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