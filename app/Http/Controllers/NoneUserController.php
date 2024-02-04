<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Core\ExponentialBackoff;
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\View;
use Kreait\Firebase\Auth as FirebaseAuth;
use Session;

class NoneUserController extends Controller
{
    
            public function NonePublishedArticleList()
            {
                try {
                    // Initialize Firestore
                    $firestore = app('firebase.firestore')->database();
                    
                    // Categories to filter
                    $categories = ['Politics', 'Economy'];
                    
                    // Initialize an empty array for article data
                    $articleData = [];
                    
                    // Fetch all users in a single query
                    $usersQuery = $firestore->collection('Users');
                    $userSnapshot = $usersQuery->documents();
                    
                    // Create an associative array of users with email as key
                    $usersByEmail = [];
                    foreach ($userSnapshot as $user) {
                        $userData = $user->data();
                        $usersByEmail[$userData['email']] = $userData;
                    }
            
                    foreach ($categories as $category) {
                        // Fetch published articles for a specific category
                        $articlesQuery = $firestore->collection('Articles')
                            ->where('Published', '=', 'yes')
                            ->where('category', '=', $category);
                        $articleSnapshot = $articlesQuery->documents();
            
                        if (!$articleSnapshot->isEmpty()) {
                            foreach ($articleSnapshot as $article) {
                                $userEmail = $article->data()['user_email'] ?? null;
                                $user = $usersByEmail[$userEmail] ?? null;
            
                                // Initialize Firebase Storage for each article
                                $storage = app('firebase.storage');
                                $bucket = $storage->getBucket();
            
                                // Handle article image
                                $imageReference = $bucket->object($article->data()['image_path']);
                                $articlePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : NULL;
            
                                // Handle user profile picture
                                $imageRef = $bucket->object($user ? $user['profile_picture'] : '../assets/img/user.jpg');
                                $profilePictureUrl = $imageRef->exists() ? $imageRef->signedUrl(now()->addMinutes(5)) : null;
            
                                $articleData[] = [
                                    'id' => $article->id(),
                                    'title' => $article->data()['title'] ?? 'N/A',
                                    'short_description' => $article->data()['short_description'] ?? 'N/A',
                                    'full_description' => $article->data()['full_description'] ?? 'N/A',
                                    'reading_time' => $article->data()['reading_time'] ?? 'N/A',
                                    'created_at' => $article->data()['created_at'] ?? 'N/A',
                                    'category' => $article->data()['category'] ?? 'N/A',
                                    'image_path' => $articlePictureUrl,
                                    'profile_picture' => $profilePictureUrl,
                                    'firstName' => $user['firstName'] ?? '',
                                    'lastName' => $user['lastName'] ?? '',
                                ];
                            }
                        }
                    }
                    
                    // Check if articleData is not empty and return the view with articles
                    if (!empty($articleData)) {
                        return ['articles' => $articleData];
                    } else {
                        return View::make('welcome', ['message' => 'No Articles Found']);
                    }
                } catch (\Exception $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }
    

            public function ArticlesOverview()
            {
                // Assume HightLight NonePublishedArticleList() and TopNews() are modified to return their respective data arrays instead of a view
                $nonePublishedArticles = $this->NonePublishedArticleList();
                $topNews = $this->TopNews();
                $hightLight = $this->HightLight();

                // Combine the data from both methods. Assuming both methods now return data arrays instead of calling View::make directly.
                $data = [
                    'articles' => $nonePublishedArticles['articles'] ?? [], // From NonePublishedArticleList
                    'articlesByCategory' => $topNews['articlesByCategory'] ?? [] ,// From TopNews
                    'highlight' => $hightLight['highlight'] ?? []
                ];
            
                // Pass combined data to the view
                return View::make('welcome', $data);
            }
            


    
            public function CategoriesArticles()
            {
                try {
                    // Initialize Firestore
                    $firestore = app('firebase.firestore')->database();
            
                    // Define the list of categories
                    $categories = ['Politics', 'Top News', 'Highlights', 'Economy', 'Conflict Zones', ];
            
                    // Fetch all users in a single query
                    $usersQuery = $firestore->collection('Users');
                    $userSnapshot = $usersQuery->documents();
            
                    // Create an associative array of users with email as key
                    $usersByEmail = [];
                    foreach ($userSnapshot as $user) {
                        $userData = $user->data();
                        $usersByEmail[$userData['email']] = $userData;
                    }
            
                    // Initialize Firebase Storage
                    $storage = app('firebase.storage');
                    $bucket = $storage->getBucket();
            
                    $articlesByCategory = [];
            
                    foreach ($categories as $category) {
                        // Fetch published articles from the specified category
                        $articlesQuery = $firestore->collection('Articles')
                                                   ->where('Published', '=', 'yes')
                                                   ->where('category', '=', $category);
                        $articleSnapshot = $articlesQuery->documents();
            
                        $articleData = [];
            
                        foreach ($articleSnapshot as $article) {
                            $articleInfo = $article->data();
                            $userEmail = $articleInfo['user_email'] ?? null;
                            $user = $usersByEmail[$userEmail] ?? null;
            
                            // Handle article image
                            $imageReference = $bucket->object($articleInfo['image_path']);
                            $articlePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;
                        // Handle user profile picture
                        $imageRef = $bucket->object($user ? $user['profile_picture'] : '../assets/img/user.jpg');
                        $profilePictureUrl = $imageRef->exists() ? $imageRef->signedUrl(now()->addMinutes(5)) : null;
            
                        $articleData[] = [
                            'id' => $article->id(),
                            'title' => $articleInfo['title'] ?? 'N/A',
                            'short_description' => $articleInfo['short_description'] ?? 'N/A',
                            'full_description' => $articleInfo['full_description'] ?? 'N/A',
                            'reading_time' => $articleInfo['reading_time'] ?? 'N/A',
                            'created_at' => $articleInfo['created_at'] ?? 'N/A',
                            'category' => $articleInfo['category'] ?? 'N/A',
                            'image_path' => $articlePictureUrl,
                            'profile_picture' => $profilePictureUrl,
                            'firstName' => $user['firstName'] ?? '',
                            'lastName' => $user['lastName'] ?? '',
                        ];
                    }
            
                    if (!empty($articleData)) {
                        $articlesByCategory[$category] = $articleData;
                    }
                }
            
                return View::make('welcome_pages.welcome_categories', ['articlesByCategory' => $articlesByCategory]);
                } catch (\Exception $e) {
                    return 'Error: ' . $e->getMessage();
                }
            
            }            

            public function TopNews()
            {
                try {
                    // Initialize Firestore
                    $firestore = app('firebase.firestore')->database();
            
                    // Define the list of categories
                    $categories = ['Top News', 'Conflict Zones'];
            
                    // Fetch all users in a single query
                    $usersQuery = $firestore->collection('Users');
                    $userSnapshot = $usersQuery->documents();
            
                    // Create an associative array of users with email as key
                    $usersByEmail = [];
                    foreach ($userSnapshot as $user) {
                        $userData = $user->data();
                        $usersByEmail[$userData['email']] = $userData;
                    }
            
                    // Initialize Firebase Storage
                    $storage = app('firebase.storage');
                    $bucket = $storage->getBucket();
            
                    $articlesByCategory = [];
            
                    foreach ($categories as $category) {
                        // Fetch published articles from the specified category
                        $articlesQuery = $firestore->collection('Articles')
                                                   ->where('Published', '=', 'yes')
                                                   ->where('category', '=', $category);
                        $articleSnapshot = $articlesQuery->documents();
            
                        $articleData = [];
            
                        foreach ($articleSnapshot as $article) {
                            $articleInfo = $article->data();
                            $userEmail = $articleInfo['user_email'] ?? null;
                            $user = $usersByEmail[$userEmail] ?? null;
            
                            // Handle article image
                            $imageReference = $bucket->object($articleInfo['image_path']);
                            $articlePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;
                        // Handle user profile picture
                        $imageRef = $bucket->object($user ? $user['profile_picture'] : '../assets/img/user.jpg');
                        $profilePictureUrl = $imageRef->exists() ? $imageRef->signedUrl(now()->addMinutes(5)) : null;
            
                        $articleData[] = [
                            'id' => $article->id(),
                            'title' => $articleInfo['title'] ?? 'N/A',
                            'short_description' => $articleInfo['short_description'] ?? 'N/A',
                            'full_description' => $articleInfo['full_description'] ?? 'N/A',
                            'reading_time' => $articleInfo['reading_time'] ?? 'N/A',
                            'created_at' => $articleInfo['created_at'] ?? 'N/A',
                            'category' => $articleInfo['category'] ?? 'N/A',
                            'image_path' => $articlePictureUrl,
                            'profile_picture' => $profilePictureUrl,
                            'firstName' => $user['firstName'] ?? '',
                            'lastName' => $user['lastName'] ?? '',
                        ];
                    }
            
                    if (!empty($articleData)) {
                        $articlesByCategory[$category] = $articleData;
                    }
                }
            
                return ['articlesByCategory' => $articlesByCategory];
                } catch (\Exception $e) {
                    return 'Error: ' . $e->getMessage();
                }
            
            }    

            public function HightLight()
            {
                try {
                    // Initialize Firestore
                    $firestore = app('firebase.firestore')->database();
            
                    // Define the list of categories
                    $categories = ['Highlights'];
            
                    // Fetch all users in a single query
                    $usersQuery = $firestore->collection('Users');
                    $userSnapshot = $usersQuery->documents();
            
                    // Create an associative array of users with email as key
                    $usersByEmail = [];
                    foreach ($userSnapshot as $user) {
                        $userData = $user->data();
                        $usersByEmail[$userData['email']] = $userData;
                    }
            
                    // Initialize Firebase Storage
                    $storage = app('firebase.storage');
                    $bucket = $storage->getBucket();
            
                    $highlight = [];
            
                    foreach ($categories as $category) {
                        // Fetch published articles from the specified category
                        $articlesQuery = $firestore->collection('Articles')
                                                   ->where('Published', '=', 'yes')
                                                   ->where('category', '=', $category);
                        $articleSnapshot = $articlesQuery->documents();
            
                        $articleData = [];
            
                        foreach ($articleSnapshot as $article) {
                            $articleInfo = $article->data();
                            $userEmail = $articleInfo['user_email'] ?? null;
                            $user = $usersByEmail[$userEmail] ?? null;
            
                            // Handle article image
                            $imageReference = $bucket->object($articleInfo['image_path']);
                            $articlePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;
                        // Handle user profile picture
                        $imageRef = $bucket->object($user ? $user['profile_picture'] : '../assets/img/user.jpg');
                        $profilePictureUrl = $imageRef->exists() ? $imageRef->signedUrl(now()->addMinutes(5)) : null;
            
                        $articleData[] = [
                            'id' => $article->id(),
                            'title' => $articleInfo['title'] ?? 'N/A',
                            'short_description' => $articleInfo['short_description'] ?? 'N/A',
                            'full_description' => $articleInfo['full_description'] ?? 'N/A',
                            'reading_time' => $articleInfo['reading_time'] ?? 'N/A',
                            'created_at' => $articleInfo['created_at'] ?? 'N/A',
                            'category' => $articleInfo['category'] ?? 'N/A',
                            'image_path' => $articlePictureUrl,
                            'profile_picture' => $profilePictureUrl,
                            'firstName' => $user['firstName'] ?? '',
                            'lastName' => $user['lastName'] ?? '',
                        ];
                    }
            
                    if (!empty($articleData)) {
                        $highlight[$category] = $articleData;
                    }
                }
            
                return ['highlight' => $highlight];
                } catch (\Exception $e) {
                    return 'Error: ' . $e->getMessage();
                }
            
            }  



    public function fullArticle($articleId)
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

                    // Return the view with the specific article and user data resources/views/welcome_pages
                    return view('welcome_pages.full_article', [
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

}
