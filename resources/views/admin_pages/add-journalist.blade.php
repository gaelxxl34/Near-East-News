@extends('/layouts.app')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>

        <style>
        .card {
             box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        }

        .form-group {
            margin-bottom: 20px;
        }

        #imagePreview {
            margin-top: 15px;
            display: block;
            max-width: 100%;
            height: auto;
        }


 
        form {
            max-width: 400px;
            margin: auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

    </style>

    <title>Add Journalist</title>
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
            <h5  style="color: black">Add Journalist👋</h5>
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

        <div class="container mt-3 mb-3">
            <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card shadow">
                    <div class="card-body">

                    <form  method="post" action="{{ route('upload.journalist') }}" enctype="multipart/form-data">
                        @csrf

                        
                        <div class="form-group">
                            <label for="image">Choose Image:</label>
                            <input type="file" name="image" id="image" class="form-control" required>
                        </div>
                     

                        <div class="form-group">
                            <label for="firstName">First Name:</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>

                        <div class="form-group">
                            <label for="lastName">Last Name:</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>


                        <button type="submit" class="btn btn-primary btn-block">Submit</button>


                        @if ($errors->has('upload_error'))
                            <p class="mt-3 text-danger">{{ $errors->first('upload_error') }}</p>
                        @endif
                    </form>

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

   


@endsection
</body>

</html>