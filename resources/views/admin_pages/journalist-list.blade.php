@extends('/layouts.app')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    .custom-table thead th {
    background-color: black;
    color: white;
}

.custom-table td, .custom-table th {
    border: 1px solid #ddd;
    padding: 8px;
}

.custom-table tr:nth-child(even){background-color: #f2f2f2;}

.custom-table tr:hover {background-color: #ddd;}

.custom-table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
}


@media screen and (max-width: 768px) {
  .table-responsive {
    overflow-x: auto;
  }
}

</style>

    <title>Journalist List</title>
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
            <h5  style="color: black">Journalist List</h5>
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

        <div class="container mt-3">
            <div class="table-responsive">

            <table class="table table-bordered custom-table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Picture</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email Address</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($journalists as $journalist)
                    <tr>
                        <td>
                            @if ($journalist['profile_picture'])
                                <img src="{{ $journalist['profile_picture'] }}" alt="User" class="rounded-circle" style="width: 55px; height: 60px; object-fit: cover;">
                            @else
                                <span>No Picture</span>
                            @endif
                        </td>
                        <td>{{ $journalist['firstName'] }}</td>
                        <td>{{ $journalist['lastName'] }}</td>
                        <td>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                {{ $journalist['email'] }}
                                <!-- Edit button -->
                                <a href="{{ route('editJournalist', ['id' => $journalist['id']]) }}" class="btn btn-sm btn-danger" style="margin-left: 10px;">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>    
                        </td>

                    </tr>
                @endforeach

                </tbody>
            </table>


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
function previewImage() {
    var preview = document.getElementById('imagePreview');
    var fileInput = document.getElementById('imageInput');
    var file = fileInput.files[0];
    var reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result;
        preview.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
        truncateFileName(fileInput);
    } else {
        preview.src = "";
        preview.style.display = 'none';
    }
}

function truncateFileName(input) {
    var fileName = input.files[0].name;
    var maxFileNameLength = Math.floor(input.offsetWidth / 10); // Assuming average character width
    if (fileName.length > maxFileNameLength) {
        var truncatedFileName = fileName.substring(0, maxFileNameLength - 3) + '...';
        input.nextElementSibling.textContent = truncatedFileName;
    } else {
        input.nextElementSibling.textContent = fileName;
    }
}


</script>
@endsection
</body>

</html>