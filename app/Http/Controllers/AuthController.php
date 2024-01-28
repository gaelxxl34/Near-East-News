<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;
use Session;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Exception\FirebaseException;
use Firebase\Firestore\Firestore;

class AuthController extends Controller
{
    protected $firebaseAuth;
    
    public function __construct()
    {
        if (env('FIREBASE_CREDENTIALS_BASE64')) {
            $firebaseCredentialsJson = base64_decode(env('FIREBASE_CREDENTIALS_BASE64'));
            if (!$firebaseCredentialsJson) {
                throw new \Exception('Failed to decode FIREBASE_CREDENTIALS_BASE64');
            }
            $serviceAccount = json_decode($firebaseCredentialsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to decode JSON: ' . json_last_error_msg());
            }
        } else {
            $firebaseCredentialsPath = env('FIREBASE_CREDENTIALS');
            if (!$firebaseCredentialsPath || !file_exists($firebaseCredentialsPath)) {
                throw new \Exception('Firebase credentials file path is not set or file does not exist');
            }
            $serviceAccount = $firebaseCredentialsPath;
        }
    
        $firebaseFactory = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
    
        $this->firebaseAuth = $firebaseFactory->createAuth();
    }
    
    



    /**
    * Show the login form.
    *
    * @return \Illuminate\Http\Response
    */
    public function showLoginForm()
    {
        return view('login');
    }

    public function Home()
    {
        return view('home'); 
    }


    public function adminDashboard()
    {
        return view('admin_dashboard');
    }



    /**
    * Authenticate the user using Firebase.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        try {
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($credentials['email'], $credentials['password']);
            $uid = $signInResult->firebaseUserId();
            
            // Store user details in session if needed
            session()->put('user_email', $credentials['email']);
            session()->put('user', $uid);

            
            $userRef = app('firebase.firestore')
                        ->database()
                        ->collection('Users')
                        ->document($uid)->snapshot();

            $userData = $userRef->data();
        
        // Check user role and redirect accordingly
        switch ($userData['role']) {
            case 'admin':
                return redirect('/admin_dashboard');
                break;
            case 'journalist':
                return redirect('/journalist_dashboard');
                break;
            case 'user':
                return redirect('/home');
                break;
            default:
                throw new \Exception("User with uid {$uid} does not exist or has no assigned role.");
        }
    } catch (\Exception $e) {
        return back()->withErrors(['login_error' => 'Invalid login credentials']);
    }
}
    

    /**
     * 
     * 
    * Log out the user.
    *
    * @return \Illuminate\Http\Response
    */
    public function logout()
    {
        session()->forget('user');
        // You can also sign out the Firebase user if needed using $this->firebaseAuth->signOut()
        
        return redirect('/');
    }
}