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

class AdminArticleController extends Controller
{
    public function adminPendingArticleList()
    {

        try {
    
            // Query Firestore to get all articles for the current user
            $articlesQuery = app('firebase.firestore')->database()->collection('Articles')->where('Published', '=', 'no');

            // Order the articles by 'created_at' in descending order
            $articlesQuery = $articlesQuery->orderBy('created_at', 'desc');
            $articleSnapshot = $articlesQuery->documents();
    
    
            // Check if any users with the specified role exist
            if (!$articleSnapshot->isEmpty()) {
                // Create an array to store user data
                $articleData = [];
    
                // Initialize Firebase Storage
                $storage = app('firebase.storage');
                $bucket = $storage->getBucket();
    
                // Populate the userDocs array

    
                // Iterate through the articles and store relevant data
                foreach ($articleSnapshot as $index => $article) {
                    // Query Firestore to get the user's profile picture, first name, and last name
                    $userEmail = $article->data()['user_email'] ?? null;
                    $usersQuery = app('firebase.firestore')->database()->collection('Users')->where('email', '=', $userEmail);
                    $userSnapshot = $usersQuery->documents();


                    $userDocs = [];
                    foreach ($userSnapshot as $userSnap) {
                        $userDocs[] = $userSnap;
                    }
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
    
                return View::make('admin_pages.pending-articles', ['articles' => $articleData]);
            } else {
                return 'No article data found now go back young man';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    
    public function adminDashboard() {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
    
        // Query for articles
        $articlesCollection = $database->collection('Articles');
        $publishedArticlesCount = $articlesCollection->where('Published', '=', 'yes')->documents()->size();
        $pendingArticlesCount = $articlesCollection->where('Published', '=', 'no')->documents()->size();
    
        // Query for users
        $usersCollection = $database->collection('Users');
        $journalistsCount = $usersCollection->where('role', '=', 'journalist')->documents()->size();
        $usersCount = $usersCollection->where('role', '=', 'user')->documents()->size();
    
        // Passing data to the view
        return view('admin_dashboard', [
            'publishedArticlesCount' => $publishedArticlesCount,
            'pendingArticlesCount' => $pendingArticlesCount,
            'journalistsCount' => $journalistsCount,
            'usersCount' => $usersCount
        ]);
    }
    



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

                // Initialize user profile picture URL and user
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
                return view('admin_pages.single-articles', [
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
    


// -- Publish the article by the admin
public function publish($id) {
    // Fetch the article from Firestore
    $articleRef = app('firebase.firestore')->database()->collection('Articles')->document($id);
    $articleSnapshot = $articleRef->snapshot();

    if ($articleSnapshot->exists()) {
        // Update the 'Published' field
        $articleRef->update([
            ['path' => 'Published', 'value' => 'yes'],
            ['path' => 'created_at', 'value' => new \DateTime()]
        ]);

        return redirect()->route('admin.published-articles')->with('success', 'Article updated successfully');
    } else {
        return redirect()->back()->with('error', 'Article not found.');
    }
}



// -- unPublish the article by the admin
public function unPublish($id) {
    // Fetch the article from Firestore
    $articleRef = app('firebase.firestore')->database()->collection('Articles')->document($id);
    $articleSnapshot = $articleRef->snapshot();

    if ($articleSnapshot->exists()) {
        // Update the 'Published' field
        $articleRef->update([
            ['path' => 'Published', 'value' => 'no'],
            ['path' => 'created_at', 'value' => new \DateTime()]
        ]);

        return redirect()->route('admin.pending-articles')->with('success', 'Article updated successfully');
    } else {
        return redirect()->back()->with('error', 'failed to unpublish.');
    }
}



// fetch published articles
public function adminPublishedArticleList()
{

    try {

        // Query Firestore to get all articles for the current user
        $articlesQuery = app('firebase.firestore')->database()->collection('Articles')->where('Published', '=', 'yes');
        $articlesQuery = $articlesQuery->orderBy('created_at', 'desc');
        $articleSnapshot = $articlesQuery->documents();


        // Check if any users with the specified role exist
        if (!$articleSnapshot->isEmpty()) {
            // Create an array to store user data
            $articleData = [];

            // Initialize Firebase Storage
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket();

            // Populate the userDocs array


            // Iterate through the articles and store relevant data
            foreach ($articleSnapshot as $index => $article) {
                // Query Firestore to get the user's profile picture, first name, and last name
                $userEmail = $article->data()['user_email'] ?? null;
                $usersQuery = app('firebase.firestore')->database()->collection('Users')->where('email', '=', $userEmail);
                $userSnapshot = $usersQuery->documents();


                $userDocs = [];
                foreach ($userSnapshot as $userSnap) {
                    $userDocs[] = $userSnap;
                }
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

            return View::make('admin_pages.published-articles', ['articles' => $articleData]);
        } else {
            return 'No article data found now go back young man';
        }
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
}




    // ArticleController.php
    public function fullPuplishedArticle($articleId)
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
                return view('admin_pages.single-published-article', [
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

}
