<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Core\ExponentialBackoff;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Kreait\Firebase\Auth as FirebaseAuth;
use Session;


class ArticleController extends Controller
{
    public function showForm()
    {
        return view('upload_article_form');
    }

    public function uploadArticle(Request $request)
    {
        
        $request->validate([
            'articleTitle' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', 
            'shortDescription' => 'required|string',
            'fullDescription' => 'required|string',
            'readingTime' => 'required|numeric',
            'category' => 'required|string',
            'region' => 'required|string',
        ]);

        try {
            // Upload image to Firebase Storage
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket();
            
            $imagePath = 'article_images/' . uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $uploadedFile = fopen($request->file('image')->path(), 'r');
            $bucket->upload($uploadedFile, ['name' => $imagePath]);

            // Add article data and image path to Firestore
            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $articlesRef = $database->collection('Articles');
            $articlesRef->add([
                'title' => $request->articleTitle,
                'image_path' => $imagePath,
                'short_description' => $request->shortDescription,
                'full_description' => $request->fullDescription,
                'reading_time' => $request->readingTime,
                'category' => $request->category,
                'region' => $request->region,
                'created_at' => new \DateTime(),
                'user_email' => session()->get('user_email') ?? Auth::user()->email,
                'Published' => 'no',
            ]);

            // Redirect to the pending.blade.php page after successful upload
        return redirect('/journalist/pending');
            
        } catch (\Throwable $e) {
            // If there was an error, return an error response
            return back()->withErrors(['upload_error' => 'Error uploading article.'])->with('message', 'Error uploading article');
        }
    }



    public function journalistDashboard() {
        $userEmail = session()->get('user_email') ?? Auth::user()->email;
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
        $articlesCollection = $database->collection('Articles')->where('user_email', '=', $userEmail);
    
        // Query to count articles where 'Published' field is 'yes'
        $publishedArticlesCount = $articlesCollection->where('Published', '=', 'yes')->documents()->size();
    

        // Query to count articles where 'Published' field is 'no'
        $pendingArticlesCount = $articlesCollection->where('Published', '=', 'no')->documents()->size();

        // Passing data to the view
        return view('journalist_dashboard', [
            'publishedArticlesCount' => $publishedArticlesCount,
            'pendingArticlesCount' => $pendingArticlesCount
        ]);
    }
    




    // --- Fetch article data method

    public function articleList()
    {
        try {
            // Retrieve the user's email from the session
            $userEmail = session()->get('user_email') ?? Auth::user()->email;
    
            // Query Firestore to get all articles for the current user
            $articlesQuery = app('firebase.firestore')->database()->collection('Articles')
            ->where('user_email', '=', $userEmail)->where('Published', '=', 'no');
    
            $articleSnapshot = $articlesQuery->documents();
    
            // Query Firestore to get the user's profile picture, first name, and last name
            $usersQuery = app('firebase.firestore')->database()->collection('Users')
                ->where('email', '=', $userEmail);
    
            $userSnapshot = $usersQuery->documents();
    
            // Check if any users with the specified role exist
            if (!$articleSnapshot->isEmpty()) {
                // Create an array to store user data
                $articleData = [];
    
                // Initialize Firebase Storage
                $storage = app('firebase.storage');
                $bucket = $storage->getBucket();
    
                // Populate the userDocs array
                $userDocs = [];
                foreach ($userSnapshot as $userSnap) {
                    $userDocs[] = $userSnap;
                }
    
                // Iterate through the articles and store relevant data
                foreach ($articleSnapshot as $index => $article) {
                    // Get the user document snapshot for this article
                    $userDoc = isset($userDocs[$index]) ? $userDocs[$index] : NULL;
    
                    // Initialize Firebase Storage for each user
                    $imageReference = app('firebase.storage')->getBucket()->object($article->data()['image_path']);
                    $articlePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : NULL;

                    $imageRef = app('firebase.storage')->getBucket()->object($userDoc ? $userDoc->data()['profile_picture'] : '../assets/img/user.jpg');
                    $profilePictureUrl = $imageRef->exists() ? $imageRef->signedUrl(now()->addMinutes(5)) : null;

    
                    $articleData[] = [
                        'id' => $article->id(),
                        'title' => $article->data()['title'] ?? 'N/A',
                        'short_description' => $article->data()['short_description'] ?? 'N/A',
                        'full_description' => $article->data()['full_description'] ?? 'N/A',
                        'reading_time' => $article->data()['reading_time'] ?? 'N/A',
                        'created_at' => $article->data()['created_at'] ?? 'N/A',
                        'image_path' => $articlePictureUrl,
                        'profile_picture' => $profilePictureUrl,
                        'firstName' => $userDoc && !empty($userDoc->data()['firstName']) ? (string)$userDoc->data()['firstName'] : '',
                        'lastName' => $userDoc && !empty($userDoc->data()['lastName']) ? (string)$userDoc->data()['lastName'] : '',
                    ];
                }
    
                return View::make('journalist pages.pending', ['articles' => $articleData]);
            } else {
                return 'No article data found now go back';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function publishedArticleList()
    {
        try {
            // Retrieve the user's email from the session
            $userEmail = session()->get('user_email') ?? Auth::user()->email;
    
            // Query Firestore to get all articles for the current user
            $articlesQuery = app('firebase.firestore')->database()->collection('Articles')
            ->where('user_email', '=', $userEmail)->where('Published', '=', 'yes');
    
            $articleSnapshot = $articlesQuery->documents();
    
            // Query Firestore to get the user's profile picture, first name, and last name
            $usersQuery = app('firebase.firestore')->database()->collection('Users')
                ->where('email', '=', $userEmail);
    
            $userSnapshot = $usersQuery->documents();
    
            // Check if any users with the specified role exist
            if (!$articleSnapshot->isEmpty()) {
                // Create an array to store user data
                $articleData = [];
    
                // Initialize Firebase Storage
                $storage = app('firebase.storage');
                $bucket = $storage->getBucket();
    
                // Populate the userDocs array
                $userDocs = [];
                foreach ($userSnapshot as $userSnap) {
                    $userDocs[] = $userSnap;
                }
    
                // Iterate through the articles and store relevant data
                foreach ($articleSnapshot as $index => $article) {
                    // Get the user document snapshot for this article
                    $userDoc = isset($userDocs[$index]) ? $userDocs[$index] : NULL;
    
                    // Initialize Firebase Storage for each user
                    $imageReference = app('firebase.storage')->getBucket()->object($article->data()['image_path']);
                    $articlePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : NULL;

                    $imageRef = app('firebase.storage')->getBucket()->object($userDoc ? $userDoc->data()['profile_picture'] : '../assets/img/user.jpg');
                    $profilePictureUrl = $imageRef->exists() ? $imageRef->signedUrl(now()->addMinutes(5)) : null;

    
                    $articleData[] = [
                        'id' => $article->id(),
                        'title' => $article->data()['title'] ?? 'N/A',
                        'short_description' => $article->data()['short_description'] ?? 'N/A',
                        'full_description' => $article->data()['full_description'] ?? 'N/A',
                        'reading_time' => $article->data()['reading_time'] ?? 'N/A',
                        'created_at' => $article->data()['created_at'] ?? 'N/A',
                        'image_path' => $articlePictureUrl,
                        'profile_picture' => $profilePictureUrl,
                        'firstName' => $userDoc && !empty($userDoc->data()['firstName']) ? (string)$userDoc->data()['firstName'] : '',
                        'lastName' => $userDoc && !empty($userDoc->data()['lastName']) ? (string)$userDoc->data()['lastName'] : '',
                    ];
                }
    
                return View::make('journalist pages.published', ['articles' => $articleData]);
            } else {
                return 'No article data found now go back';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }





    // ArticleController.php
public function fullPendingArticle($articleId)
{
    try {
        // Fetch the specific article based on the ID
        $articleSnapshot = app('firebase.firestore')->database()->collection('Articles')->document($articleId)->snapshot();

        // Get the user email associated with the article
        $userEmail = $articleSnapshot->data()['user_email'];

        // Query Firestore to get the user's profile picture, first name, and last name
        $userQuery = app('firebase.firestore')->database()->collection('Users')->where('email', '=', $userEmail);

        $userSnapshot = $userQuery->documents();

        // Check if the user exists
        if (!$userSnapshot->isEmpty()) {
            // Fetch download URLs for article picture and user profile picture from Firebase Storage
            $articlePictureUrl = $this->getDownloadUrl($articleSnapshot->data()['image_path']);

            // Initialize user profile picture URL and user data
            $userProfilePictureUrl = null;
            $userData = [];

            // Iterate over the user documents
            foreach ($userSnapshot as $user) {
                $userProfilePictureUrl = $this->getDownloadUrl($user->data()['profile_picture']);

                // Add user data to the array
                $userData = [
                    'profile_picture' => $userProfilePictureUrl,
                    'firstName' => $user->data()['firstName'] ?? '',
                    'lastName' => $user->data()['lastName'] ?? '',
                ];
                
                // Break the loop after the first user document (if you only expect one)
                break;
            }

            // Return the view with the specific article and user data
            return view('journalist pages.full-pending-article', [
                'article' => array_merge($articleSnapshot->data(), ['id' => $articleSnapshot->id(), 'image_path' => $articlePictureUrl]),
                'user' => $userData,
            ]);
            
        } else {
            // Handle the case where the user associated with the article is not found
            return 'User not found for the specified article.';
        }

    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
}


/**
 * Helper function to get the download URL for a file in Firebase Storage.
 *
 * @param string $filePath
 * @return string|null
 */
private function getDownloadUrl($filePath)
{
    try {
        $imageReference = app('firebase.storage')->getBucket()->object($filePath);
        return $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;
    } catch (\Exception $e) {
        return null;
    }
}





public function editArticle($articleId)
{
    try {
        // Query Firestore to get the article with the specified $articleId
        $articleRef = app('firebase.firestore')->database()->collection('Articles')->document($articleId);
        $articleSnapshot = $articleRef->snapshot();

        $articlePictureUrl = $this->getDownloadUrl($articleSnapshot->data()['image_path']);
        // Check if the article exists
        if ($articleSnapshot->exists()) {
            // Extract article data from the snapshot
            $articleData = [
                'id' => $articleSnapshot->id(),
                'title' => $articleSnapshot->data()['title'] ?? '',
                'image_path' => $articlePictureUrl,
                'short_description' => $articleSnapshot->data()['short_description'] ?? '',
                'full_description' => $articleSnapshot->data()['full_description'] ?? '',
                'reading_time' => $articleSnapshot->data()['reading_time'] ?? '',
                'category' => $articleSnapshot->data()['category'] ?? '',
                'region' => $articleSnapshot->data()['region'] ?? '',
                // Add other fields as needed
            ];

            // Pass the data to the edit view
            return View::make('journalist pages.edit-article', ['article' => $articleData]);
        } else {
            // Handle the case where the article doesn't exist
            return 'No article data available';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
}



// ...

public function updateArticle(Request $request, $articleId)
{
    try {
        // Query Firestore to get the article with the specified $articleId
        $articleRef = app('firebase.firestore')->database()->collection('Articles')->document($articleId);
        $articleSnapshot = $articleRef->snapshot();

        // Check if the article exists
        if ($articleSnapshot->exists()) {
            // Validate the form data
            $request->validate([
                'articleTitle' => 'required|string|max:255',
                'image' => 'image|mimes:jpeg,png,jpg,gif', // Adjust as needed 
                'shortDescription' => 'required|string',
                'fullDescription' => 'required|string',
                'readingTime' => 'required|numeric',
                'category' => 'required|string',
                'region' => 'required|string',
            ]);

            // Initialize update data array
            $updateData = [
                ['path' => 'title', 'value' => $request->input('articleTitle')],
                ['path' => 'short_description', 'value' => $request->input('shortDescription')],
                ['path' => 'full_description', 'value' => $request->input('fullDescription')],
                ['path' => 'reading_time', 'value' => $request->input('readingTime')],
                ['path' => 'category', 'value' => $request->input('category')],
                ['path' => 'region', 'value' => $request->input('region')],
                ['path' => 'created_at', 'value' => new \DateTime()]

                // Add other fields as needed
            ];

            // Check if a new image is uploaded
            if ($request->hasFile('image')) {
                // Retrieve the old image path from Firestore
                $oldImagePath = $articleSnapshot['image_path']; // Assuming 'image_path' is just the path

                // Delete the old image from Firebase Storage, if it exists
                if ($oldImagePath) {
                    try {
                        $firebaseStorage = app('firebase.storage');
                        $firebaseStorage->getBucket()->object($oldImagePath)->delete();
                    } catch (\Exception $e) {
                        return back()->withErrors(['error' => 'Error deleting existing image'])->with('message', 'Error deleting existing image');
                        Log::error('Error deleting image: ' . $e->getMessage());
                    }
                }

                // Upload the new image
                $image = $request->file('image');
                $newImageName = 'article_images/' . time() . '.' . $image->getClientOriginalExtension();
                $firebaseStorage->getBucket()->upload(
                    file_get_contents($image),
                    ['name' => $newImageName]
                );

                // Add new image path to update data
                $updateData[] = ['path' => 'image_path', 'value' => $newImageName];
            }

            // Update the Firestore document
            $articleRef->update($updateData);

            return redirect()->route('journalist.edit-article', ['articleId' => $articleId])->with('success', 'Article updated successfully');
        } else {
            // Handle the case where the article doesn't exist
            return 'No article data available';
        }
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
}


// ...


public function deleteArticle($articleId)
{
    try {
        // Get the reference to the article in Firestore
        $articleRef = app('firebase.firestore')->database()->collection('Articles')->document($articleId);
        $articleSnapshot = $articleRef->snapshot();

        // Check if the article exists
        if ($articleSnapshot->exists()) {
            // Get the image path
            $imagePath = $articleSnapshot['image_path'] ?? null;

            // Delete the image from Firebase Storage, if it exists
            if ($imagePath) {
                $firebaseStorage = app('firebase.storage');
                $firebaseStorage->getBucket()->object($imagePath)->delete();
                Log::info('Image for article ID ' . $articleId . ' deleted successfully from Firebase Storage.');
            }

            // Delete the document from Firestore
            $articleRef->delete();
            Log::info('Article with ID ' . $articleId . ' deleted successfully from Firestore.');

            return redirect()->route('journalist.pending')->with('success', 'Article deleted successfully');
        } else {
            return 'Article not found';
        }
    } catch (\Exception $e) {
        Log::error('Error deleting article with ID ' . $articleId . ': ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
}

}

