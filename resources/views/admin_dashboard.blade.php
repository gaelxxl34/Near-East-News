@extends('layouts.app')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        
    <title>Admin Dashboard</title>
</head>

<body>

@section('content')
        <!-- Main Component -->
        <div class="main">

        <nav class="navbar navbar-expand px-2 border-bottom d-flex justify-content-between">
            <!-- Button for sidebar toggle -->
            <button class="btn" type="button">
                <span class="navbar-toggler-icon" style="color: black;"></span>
            </button>

            <!-- User Email and Logout -->
            <div class="d-flex">
                @if(session('user_email'))
                    <div style="display: flex; align-items: center;">
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
        </nav>

            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <div class="mb-3">
                        <h2  style="color: black">Welcome Dear Admin!!</h2>
                    </div>
                </div>


                <div id="apexcharts-area"></div>

                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-12 mb-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fa-solid fa-newspaper"></i> Published Articles</h5>
                                    <p class="card-text">{{ $publishedArticlesCount }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fa-solid fa-users"></i> Journalists</h5>
                                    <p class="card-text">{{ $journalistsCount }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fa-solid fa-clock"></i> Pending Articles</h5>
                                    <p class="card-text">{{ $pendingArticlesCount }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fa-solid fa-chart-line"></i> All Users</h5>
                                    <p class="card-text">{{ $usersCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                
            </main>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>


    <script>
        const toggler = document.querySelector(".btn");
        toggler.addEventListener("click",function(){
            document.querySelector("#sidebar").classList.toggle("collapsed");
        });

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

    <script>
            var options = {
    chart: {
        height: 350,
        type: "area",
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: "smooth"
    },
    series: [{
        name: "series1",
        data: [31, 40, 28, 51, 42, 109, 100]
    }, {
        name: "series2",
        data: [11, 32, 45, 32, 34, 52, 41]
    }],
    xaxis: {
        type: "datetime",
        categories: ["2018-09-19T00:00:00", "2018-09-19T01:30:00", "2018-09-19T02:30:00", "2018-09-19T03:30:00", "2018-09-19T04:30:00", "2018-09-19T05:30:00", "2018-09-19T06:30:00"],
    },
    tooltip: {
        x: {
            format: "dd/MM/yy HH:mm"
        },
    }
}
var chart = new ApexCharts(document.querySelector("#apexcharts-area"), options);
chart.render();
    </script>

@endsection
</body>

</html>