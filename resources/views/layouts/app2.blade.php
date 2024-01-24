<!-- resources/views/layouts/app2.blade.php -->
<!DOCTYPE html>
<html lang="en">


<head>
    <!-- Head Contents -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

        <link href="\assets\css\admin dashboard.css" rel="stylesheet">
</head>
<body>
    
    <div class="wrapper">
        @include('partials.journalist-sidebar')
        <div class="main">
            @yield('content')
        </div>
    </div>

</body>
</html>
