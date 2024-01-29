<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\FirebaseRegisterController;
use App\Http\Controllers\RegisterJournalistController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\NoneUserController; 
use App\Http\Controllers\UserArticleController;


// Firebase Authentication Routes
Route::post('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');





// Firebase registration Routes
Route::post('register', [FirebaseRegisterController::class, 'register']);
Route::get('register', [FirebaseRegisterController::class, 'showRegistrationForm'])->name('register');
Route::get('home', [FirebaseRegisterController::class, 'Home'])->name('home');
Route::post('home', [FirebaseRegisterController::class, 'Home'])->name('home');


Route::get('logout', 'App\Http\Controllers\AuthController@logout')->name('logout');
Route::post('logout', 'App\Http\Controllers\AuthController@logout')->name('logout');    
// Define a route for the forget password form submission
Route::post('forget-password', [FirebaseAuthController::class, 'sendPasswordResetLink'])->name('forget-password.action');
Route::get('/forget-password', [FirebaseAuthController::class, 'showForgetPasswordForm'])->name('forget-password');



// -- Main route based on user role
Route::get('home', [AuthController::class, 'Home'])->name('home');

Route::get('/admin_dashboard', [AdminArticleController::class, 'adminDashboard'])
->name('admin_dashboard');


Route::get('/journalist_dashboard', [ArticleController::class, 'journalistDashboard'])
->name('journalist_dashboard');
// ... end of user role ...




// -- Admin pages routes published-articles
Route::get('/upload-journalist', [RegisterJournalistController::class, 'showForm']);
Route::post('/upload-journalist', [RegisterJournalistController::class, 'registerJournalist'])
->name('upload.journalist');

Route::put('/admin/update-journalist-data/{journalistId}', [RegisterJournalistController::class, 'updateJournalist'])
    ->name('admin.update-journalist-data');



// -- fetching data list of journalist and pending articles
Route::get('/admin/pending-articles', [AdminArticleController::class, 'adminPendingArticleList'])
->name('admin.pending-articles');

Route::get('/admin/published-articles', [AdminArticleController::class, 'adminPublishedArticleList'])
->name('admin.published-articles');

Route::get('/admin/journalist-list', [RegisterJournalistController::class, 'journalistList'])->name('admin.journalist-list');

Route::get('/admin/single-articles/{articleId}', [AdminArticleController::class, 'fullPendingArticle'])
    ->name('admin.single-articles');

Route::post('/admin/single-articles/{articleId}', [AdminArticleController::class, 'fullPendingArticle'])
    ->name('admin.single-articles');


Route::get('/admin/single-published-article/{articleId}', [AdminArticleController::class, 'fullPuplishedArticle'])
    ->name('admin.single-published-article');

Route::post('/admin/single-published-article/{articleId}', [AdminArticleController::class, 'fullPuplishedArticle'])
    ->name('admin.single-published-article');

Route::get('/publish-article/{id}',[AdminArticleController::class, 'publish']);

Route::get('/unpublish-article/{id}',[AdminArticleController::class, 'unPublish']);


Route::get('/admin/add-journalist', function () {
    return view('admin_pages.add-journalist');
})->name('admin.add-journalist');
// -- End of the admin pages









// Journalist pages routes published-articles

Route::get('/journalist/add-articles', function () {
    return view('journalist pages.add-articles');
})->name('journalist.add-articles');


// -- upload articles
Route::get('/upload', [ArticleController::class, 'showForm']);
Route::post('/upload', [ArticleController::class, 'uploadArticle'])->name('upload.article.submit');


// fetch full article data and pending article data
Route::get('/journalist/pending', [ArticleController::class, 'articleList'])
->name('journalist.pending');


Route::get('/journalist/published', [ArticleController::class, 'publishedArticleList'])
->name('journalist.published');


Route::get('/journalist/full-pending-article/{articleId}', [ArticleController::class, 'fullPendingArticle'])
    ->name('journalist.full-pending-article');

Route::post('/journalist/full-pending-article/{articleId}', [ArticleController::class, 'fullPendingArticle'])
    ->name('journalist.full-pending-article');

// -- Edit or update & delete article data journalist side 

Route::get('/journalist/edit-article/{articleId}', [ArticleController::class, 'editArticle'])
    ->name('journalist.edit-article');


Route::put('/journalist/update-article/{articleId}', [ArticleController::class, 'updateArticle'])
    ->name('journalist.update-article');

Route::delete('/delete-article/{articleId}',[ArticleController::class, 'deleteArticle'])->name('delete-article');

Route::get('/edit-journalist/{id}',[RegisterJournalistController::class, 'editJournalist'])->name('editJournalist');

// -- End of journalist pages and actions -- //






// user pages routes
Route::get('/home', [UserArticleController::class, 'UserPublishedArticleList'])
->name('home');


Route::get('/user/regions', [UserArticleController::class, 'UserRegionsArticles'])
->name('user.regions');


Route::get('/user/categories', [UserArticleController::class, 'CategoriesArticles'])
->name('user.categories');


Route::get('/user/contact', function () {
    return view('user pages.contact');
})->name('user.contact');

Route::get('/user/favorite', function () {
    return view('user pages.favorite');
})->name('user.favorite');
// -- end of user pages 


/* 
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', [NoneUserController::class, 'NonePublishedArticleList'])
->name('welcome');


Route::get('/welcome', [NoneUserController::class, 'NonePublishedArticleList'])
->name('welcome');




Route::get('/welcome-categories', [NoneUserController::class, 'CategoriesArticles'])
->name('welcome.categories');


Route::get('/welcome-contact', function () {
    return view('welcome_pages.welcome_contact');
})->name('welcome.contact');

