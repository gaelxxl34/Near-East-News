<!-- resources/views/layouts/app3.blade.php -->
<!DOCTYPE html>
<html lang="en">


<head>
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
    <link href="..\assets\css\style.css" rel="stylesheet">
    <style>
        p{
            font-size: 13px;
        }
        .large-margin-bottom {
            margin-bottom: 80px; /* Adjust this value as needed */
        }

    </style>
</head>
<body>
    

        @include('partials.navbar')
        <div class="main">
            @yield('content')
        </div>
        @include('partials.footer')


</body>
</html>
