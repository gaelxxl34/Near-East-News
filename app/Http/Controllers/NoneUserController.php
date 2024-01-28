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
            // fetch published articles
            public function NonePublishedArticleList()
            {
                try {
                    // Initialize Firestore
                    $firestore = app('firebase.firestore')->database();
            
                    // Fetch published articles
                    $articlesQuery = $firestore->collection('Articles')->where('Published', '=', 'yes');
                    $articlesQuery = $articlesQuery->orderBy('created_at', 'desc');
                    $articleSnapshot = $articlesQuery->documents();
            
                    // Fetch all users in a single query
                    $usersQuery = $firestore->collection('Users');
                    $userSnapshot = $usersQuery->documents();
            
                    // Create an associative array of users with email as key
                    $usersByEmail = [];
                    foreach ($userSnapshot as $user) {
                        $userData = $user->data();
                        $usersByEmail[$userData['email']] = $userData;
                    }
            
                    $articleData = [];
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
                        return View::make('welcome', ['articles' => $articleData]);
                    } else {
                        return View::make('welcome', ['message' => 'No Articles Found']);
                    }
                } catch (\Exception $e) {
                    return 'Error: ' . $e->getMessage();
                }
            }
    
    
            public function CategoriesArticles()
            {
                try {
                    // Initialize Firestore
                    $firestore = app('firebase.firestore')->database();
            
                    // Define the list of categories
                    $categories = ['Politics', 'Business', 'Corporate', 'Sports', 'Health', 'Education', 'Science', 'Technology', 'Foods', 'Travel', 'Lifestyle', 'topStories', 'Entertainment'];
            
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

}
