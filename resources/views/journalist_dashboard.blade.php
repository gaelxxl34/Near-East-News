@extends('layouts.app2')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <style>
        /* Custom styles */
        .dashboard-box {
            background-color: #f8f9fa; /* Light gray background color */
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        /* Strong and vibrant background colors for each box */
        .pending-box { background-color: #FF8C00; } /* Dark Orange */
        .published-box { background-color: #4CAF50; } /* Green */
        .views-box { background-color: cyan; } /* Orange Red */


  /* Custom styles for the calendar */
  .calendar-container {
           
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.6);
            border: 10px;
        }

        .calendar-header {
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .calendar-day {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            cursor: pointer;
        }

        .current-month {
            font-weight: bold;
            color: #007BFF; /* Dark Blue */
        }

        .event-day {
            background-color: #FF8C00; /* Dark Orange */
            color: #fff;
        }
    </style>
    <title>Journalist Dashboard</title>
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
                        <h3  style="color: black">Welcome Dear journalist!!</h3>
                    </div>
                </div>

                <div class="container mt-4">
                    <div class="row">
                        <!-- Box 1: Pending Articles -->
                        <div class="col-md-4">
                            <div class="dashboard-box pending-box">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Pending Articles</h5>
                                        <p class="mb-0">{{ $pendingArticlesCount }}</p>
                                    </div>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Box 2: Published Articles -->
                        <div class="col-md-4">
                            <div class="dashboard-box published-box">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Published Articles</h5>
                                        <p class="mb-0">{{ $publishedArticlesCount }}</p>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Box 3: Total Views -->
                        <div class="col-md-4">
                            <div class="dashboard-box views-box">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5>Total Views</h5>
                                        <p class="mb-0">5,000</p>
                                    </div>
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="overflow-auto">
                                <div class="calendar-container">
                                    <div class="calendar-header">
                                        <button class="btn btn-light float-start" onclick="prevMonth()">Previous</button>
                                        <h5 id="currentMonth" class="mb-0"></h5>
                                        <button class="btn btn-light float-end" onclick="nextMonth()">Next</button>
                                    </div>
                                    <div class="calendar-days" id="calendarDays"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>



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

<!-- Bootstrap JS and additional scripts can be included here -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple JavaScript to create a basic calendar
    let currentDate = new Date();

    function renderCalendar() {
        const currentMonth = currentDate.getMonth();
        const daysInMonth = new Date(currentDate.getFullYear(), currentMonth + 1, 0).getDate();
        const firstDayOfMonth = new Date(currentDate.getFullYear(), currentMonth, 1).getDay();

        const calendarDays = document.getElementById('calendarDays');
        calendarDays.innerHTML = '';

        for (let i = 0; i < firstDayOfMonth; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day';
            calendarDays.appendChild(emptyDay);
        }

        for (let i = 1; i <= daysInMonth; i++) {
            const calendarDay = document.createElement('div');
            calendarDay.className = 'calendar-day';
            calendarDay.innerHTML = i;

            if (i === new Date().getDate() && currentMonth === new Date().getMonth()) {
                calendarDay.classList.add('current-month');
            }

            calendarDay.addEventListener('click', () => alert(`Day ${i} clicked!`));

            calendarDays.appendChild(calendarDay);
        }

        document.getElementById('currentMonth').innerText = new Date(currentDate).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    }

    function prevMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }

    document.addEventListener('DOMContentLoaded', renderCalendar);
</script>
@endsection
</body>

</html>