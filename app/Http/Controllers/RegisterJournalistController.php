<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Auth\UserRecord;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Firestore;
use Illuminate\Support\Facades\View;



class RegisterJournalistController extends Controller
{
    protected $auth;

    public function __construct()
    {    
        $firebaseFactory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
    
        $this->auth = $firebaseFactory->createAuth();
    }



    
    public function showForm()
    {
        return view('upload_form');
    }
    public function registerJournalist(Request $request)
    {
        \Log::info("registerJournalist called");
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        try {
            // Create a new user in Firebase Authentication
            $userProperties = [
                'email' => $request->email,
                'emailVerified' => false,
                'password' => $request->password,
                'disabled' => false,
            ];
    
           
    
            $createdUser = $this->auth->createUser($userProperties);
    
            // Initialize Firebase Storage
            $storage = app('firebase.storage');
            $bucket = $storage->getBucket(); // Use the default bucket
    
            // Upload image to Firebase Storage
            $imagePath = 'journalist_images/' . uniqid() . '_' . $request->file('image')->getClientOriginalName();
            $uploadedFile = fopen($request->file('image')->path(), 'r');
            $bucket->upload($uploadedFile, ['name' => $imagePath]);
    
            // Add user data and image path to Firestore
            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $usersRef = $database->collection('Users');
            $usersRef->document($createdUser->uid)->set([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'profile_picture' => $imagePath,
                'role' => 'journalist'
            ]);
            // Return success response
            return redirect()->intended('/admin/journalist-list'); // Redirect to 'home' or any other route
        } catch (\Throwable $e) {
            \Log::error('Error in registerJournalist: ' . $e->getMessage());
            // If there was an error, return an error response
            return back()->withErrors(['upload_error' => 'Error uploading image.'])->with('message', 'Error uploading image');
        }
    }


    public function journalistList(){
        try {
            // Query Firestore to get all users with role 'journalist'
            $usersQuery = app('firebase.firestore')->database()->collection('Users')->where('role', '=', 'journalist');
            $JournalistSnapshot = $usersQuery->documents();
    
            // Check if any users with the specified role exist
            if (!$JournalistSnapshot->isEmpty()) {
                // Create an array to store user data
                $userData = [];
    
                  // Initialize Firebase Storage
                  $storage = app('firebase.storage');
                  $bucket = $storage->getBucket();
      
                  // Iterate through the users and store relevant data
                   // Iterate through the users and store relevant data
                foreach ($JournalistSnapshot as $journalist) {
                    // Initialize Firebase Storage for each user
                    $imageReference = app('firebase.storage')->getBucket()->object($journalist->data()['profile_picture']);
                    $profilePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;
    
                    $userData[] = [
                        'id' => $journalist->id(),
                        'firstName' => $journalist->data()['firstName'] ?? 'N/A',
                        'lastName' => $journalist->data()['lastName'] ?? 'N/A',
                        'email' => $journalist->data()['email'] ?? 'N/A',
                        'profile_picture' => $profilePictureUrl,
                    ];
                }
                
    
                return View::make('admin_pages.journalist-list', ['journalists' => $userData]);
            } else {
                return 'No journalists found';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
   


    public function editJournalist($id){
        try {
            // Get a reference to the Firestore database
            $database = app('firebase.firestore')->database();
    
            // Query Firestore to get the journalist by ID
            $journalistRef = $database->collection('Users')->document($id);
            $journalistSnapshot = $journalistRef->snapshot();
    
            if ($journalistSnapshot->exists()) {
                // Initialize Firebase Storage
                $storage = app('firebase.storage');
                $bucket = $storage->getBucket();
    
                // Fetch profile picture URL
                $imageReference = $bucket->object($journalistSnapshot->data()['profile_picture']);
                $profilePictureUrl = $imageReference->exists() ? $imageReference->signedUrl(now()->addMinutes(5)) : null;
    
                // Prepare journalist data
                $journalistData = [
                    'id' => $journalistSnapshot->id(),
                    'firstName' => $journalistSnapshot->data()['firstName'] ?? 'N/A',
                    'lastName' => $journalistSnapshot->data()['lastName'] ?? 'N/A',
                    'email' => $journalistSnapshot->data()['email'] ?? 'N/A',
                    'profile_picture' => $profilePictureUrl,
                ];
    
                return view('admin_pages.edit-data', ['journalist' => $journalistData]);
            } else {
                return 'Journalist not found';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    

    public function updateJournalist(Request $request, $id) {
        try {
            // Validation
            $validatedData = $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'profilePicture' => 'nullable|image|mimes:jpeg,png,jpg,gif', // Adjusted validation rule
            ]);
    
            // Initialize Firebase services
            $firestore = app('firebase.firestore')->database();
            $auth = app('firebase.auth');
            $storage = app('firebase.storage')->getBucket();

               // Update Firestore Data
               $journalistRef = $firestore->collection('Users')->document($id);
               $journalistRef->update([
                   ['path' => 'firstName', 'value' => $validatedData['firstName']],
                   ['path' => 'lastName', 'value' => $validatedData['lastName']],
                   ['path' => 'email', 'value' => $validatedData['email']], // Update email in Firestore
                   // ... other non-image fields ...
               ]);
    
            // Update Firestore Data
            $journalistRef = $firestore->collection('Users')->document($id);
            $journalistSnapshot = $journalistRef->snapshot();
    
            // Update Profile Picture in Firebase Storage if provided
            if ($request->hasFile('profilePicture')) { // Make sure the field name matches
                // Retrieve the old image path from Firestore
                $oldImagePath = $journalistSnapshot->data()['profile_picture']; // Corrected access to Firestore snapshot data
    
                // Delete the old image from Firebase Storage, if it exists
                if ($oldImagePath) {
                    $storage->object($oldImagePath)->delete();
                }
    
                // Upload the new image
                $image = $request->file('profilePicture'); // Adjusted field name
                $newImageName = 'journalist_images/' . time() . '.' . $image->getClientOriginalExtension();
                $storage->upload(
                    file_get_contents($image->getRealPath()),
                    ['name' => $newImageName]
                );
    
                // Update Firestore with new image path
                $journalistRef->update([
                    ['path' => 'profile_picture', 'value' => $newImageName],
                ]);
            }
    
            // Additional Firestore updates for other fields
            // ...
    
            // Update Firebase Authentication Email
            $user = $auth->getUser($id);
            if ($validatedData['email'] != $user->email) {
                $auth->changeUserEmail($id, $validatedData['email']);
            }
    
            return back()->with('success', 'Journalist updated successfully.');
    
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating journalist: ' . $e->getMessage());
        }
    }
    
    

}

           
   