@extends('/layouts.app2')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <style>
        /* Custom styles */
        .custom-card {
            width: 80%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>

    <title>Edit Article</title>
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
            <h5  style="color: black">Edit Article</h5>
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

        <div class="container mt-3 mb-3 text-center">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card custom-card">
                <div class="card-body">

                    <form method="POST" action="{{ route('journalist.update-article', ['articleId' => $article['id']]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Article Title -->
                        <div class="mb-3">
                            <label for="articleTitle" class="form-label">Article Title</label>
                            <input type="text" class="form-control" id="articleTitle" name="articleTitle" value="{{ $article['title'] }}">
                        </div>

                        <!-- Article Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Article Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            @if ($article['image_path'])
                                <img src="{{ $article['image_path'] }}" alt="Current Image" style="max-height: 200px;">
                            @endif
                        </div>

                        <!-- Short Description -->
                        <div class="mb-3">
                            <label for="shortDescription" class="form-label">Short Description</label>
                            <textarea class="form-control" id="shortDescription" name="shortDescription">{{ $article['short_description'] }}</textarea>
                        </div>

                        <!-- Full Description -->
                        <div class="mb-3">
                            <label for="fullDescription" class="form-label">Full Description</label>
                            <textarea class="form-control" id="fullDescription" name="fullDescription">{{ $article['full_description'] }}</textarea>
                        </div>

                        <!-- Reading Time -->
                        <div class="mb-3">
                            <label for="readingTime" class="form-label">Reading Time (minutes)</label>
                            <input type="number" class="form-control" id="readingTime" name="readingTime" value="{{ $article['reading_time'] }}">
                        </div>


                        <!-- Region -->
                        <div class="mb-3">
                            <label for="region" class="form-label">region</label>
                            <select name="region" id="region" class="form-control" required>
                                <option value="United Arab Emirates" {{ $article['region'] == "United Arab Emirates" ? 'selected' : '' }}>United Arab Emirates</option>
                                <option value="Egypt" {{ $article['region'] == 'Egypt' ? 'selected' : '' }}>Egypt</option>
                                <option value="Algeria" {{ $article['region'] == 'Algeria' ? 'selected' : '' }}>Algeria</option>
                                <option value="Iran" {{ $article['region'] == 'Iran' ? 'selected' : '' }}>Iran</option>
                                <option value="Iraq" {{ $article['region'] == 'Iraq' ? 'selected' : '' }}>Iraq</option>
                                <option value="Israel" {{ $article['region'] == 'Israel' ? 'selected' : '' }}>Israel</option>
                                <option value="Jordan" {{ $article['region'] == 'Jordan' ? 'selected' : '' }}>Jordan</option>
                                <option value="Libya" {{ $article['region'] == 'Libya' ? 'selected' : '' }}>Libya</option>
                                <option value="Bahrain" {{ $article['region'] == 'Bahrain' ? 'selected' : '' }}>Bahrain</option>
                                <option value="Qatar" {{ $article['region'] == 'Qatar' ? 'selected' : '' }}>Qatar</option>
                                <option value="Saudi Arabia" {{ $article['region'] == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia</option>
                                <option value="Yemen" {{ $article['region'] == 'Yemen' ? 'selected' : '' }}>Yemen</option>
                                <option value="Syrian Arab Republic" {{ $article['region'] == 'Syrian Arab Republic' ? 'selected' : '' }}>Syrian Arab Republic</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="Politics" {{ $article['category'] == 'Politics' ? 'selected' : '' }}>Politics</option>
                                <option value="Business" {{ $article['category'] == 'Business' ? 'selected' : '' }}>Business</option>
                                <option value="Corporate" {{ $article['category'] == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                                <option value="Sports" {{ $article['category'] == 'Sports' ? 'selected' : '' }}>Sports</option>
                                <option value="Health" {{ $article['category'] == 'Health' ? 'selected' : '' }}>Health</option>
                                <option value="Education" {{ $article['category'] == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Science" {{ $article['category'] == 'Science' ? 'selected' : '' }}>Science</option>
                                <option value="Technology" {{ $article['category'] == 'Technology' ? 'selected' : '' }}>Technology</option>
                                <option value="Foods" {{ $article['category'] == 'Foods' ? 'selected' : '' }}>Foods</option>
                                <option value="Entertainment" {{ $article['category'] == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                <option value="Travel" {{ $article['category'] == 'Travel' ? 'selected' : '' }}>Travel</option>
                                <option value="Lifestyle" {{ $article['category'] == 'Lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                            </select>
                        </div>


                        <!-- Update Article Button -->
                        <button type="submit" class="btn btn-success">Update Article</button>

                        <!-- Delete Article Button -->

                        <a href="javascript:;" 
                            onclick="if(confirm('Are you sure you want to delete this article?')) { document.getElementById('deleteForm').submit(); }" 
                            class="btn btn-danger">
                                Delete <i class="fas fa-trash-alt"></i>
                            </a>



                        <!-- Add this code wherever you want to display error messages -->
                            @if(session('error'))
                                <div class="alert alert-danger mt-2">
                                    {{ session('error') }}
                                </div>
                            @endif

                    </form>

                        <!-- Hidden Form -->
                        <form id="deleteForm" action="{{ route('delete-article', ['articleId' => $article['id']]) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
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