<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CurrentAudienceQuestionController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\CurrentTestController;
use App\Http\Controllers\ExtractionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HandleTeamsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManualTestController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TeamController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\Iterator\FilecontentFilterIterator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false
]);

// Route::get('t', function () {
//     User::create([
//         'id' => 1,
//         'name' => 'Admin',
//         'email' => 'admin@gmail.com',
//         'email_verified_at' => now(),
//         'password' => Hash::make('password'),
//         'remember_token' => 'jklj;joijklnkn',
//         'role' => 'admin',
//     ]);
// });

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/', [HomeController::class, 'index'])->name('index');
});
Route::middleware(['auth', 'admin'])->group(function () {

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('tests', TestController::class);
    Route::Resource('competitions', CompetitionController::class);
    Route::Resource('audiences', AudienceController::class);
    // manual test
    Route::get('manual-tests/{test}', [ManualTestController::class, 'index'])->name('manual-tests.index');
    Route::post('manual-tests/set-question', [ManualTestController::class, 'setQuestion'])->name('manual-tests.setQuestion');

    Route::get('admin/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('groups/add-team', [GroupController::class, 'addTeam'])->name('groups.add-team');
    Route::post('groups/remove-team', [GroupController::class, 'removeTeam'])->name('groups.remove-team');
    Route::post('groups/create/with-teams', [GroupController::class, 'createGroupWithTeams'])->name('groups.create.with-teams');

    Route::get('tests/{test}/questions', [TestController::class, 'showQuestions'])->name('tests.questions');
    Route::post('tests/questions', [TestController::class, 'addQuestions'])->name('tests.questions.add');
    Route::post('test-questions/{questionTest}', [TestController::class, 'removeQuestion'])->name('tests-questions.remove');
    Route::post('test-questions/add-questions/auto', [TestController::class, 'addQuestionsAuto'])->name('tests-questions.auto');
    Route::post('test-questions/add-questions/category', [TestController::class, 'addQuestionsByCategories'])->name('tests-questions.category');

    Route::post('current-audience-questions/set', [CurrentAudienceQuestionController::class, 'set'])->name('audience-questions.set');
    Route::get('current-audience-questions/{test_id}/show-question/{show}', [CurrentAudienceQuestionController::class, 'showQuestion'])->name('audience-questions.show-question');
    Route::get('current-audience-questions/{testId}/show-answer/{show}', [CurrentAudienceQuestionController::class, 'showAnswer'])->name('audience-questions.show-answer');
});


Route::middleware('auth')->group(function () {
    Route::get('handle-teams', [HandleTeamsController::class, 'index'])->name('handle-teams.index');
    Route::get('team-test/{test}/view', [HandleTeamsController::class, 'viewTest'])->name('tests.view');
});
Route::get('view', [HandleTeamsController::class, 'show']);

Route::get('current-test/{test}/question', [CurrentTestController::class, 'getQuestion'])
    ->name('current-test.question');

Route::get('current-test/{question}/{test}/answer', [CurrentTestController::class, 'correctAnswer'])
    ->name('current-test.answer');

//////////////////
Route::post('current-test/{test}/send-answer', [CurrentTestController::class, 'sendAnswer'])
    ->name('current-test.sendAnswer');

Route::get('groups/{group}/standing', [GroupController::class, 'standing'])->name('groups.standing');


Route::get('/admin/{testId}/update-tests-data', [AdminController::class, 'updateTestsData']);

// manual test

Route::get('manual-test/{test}/question', [ManualTestController::class, 'getQuestion'])
    ->name('manual-test.question');

Route::get('manual-test/{test}/end-test', [ManualTestController::class, 'endTest'])
    ->name('manual-test.endTest');

Route::get('manual-test/{question}/{test}/answer', [ManualTestController::class, 'correctAnswer'])
    ->name('manual-test.answer');

//////////////////
Route::post('manual-test/{test}/send-answer', [ManualTestController::class, 'sendAnswer'])
    ->name('manual-test.sendAnswer');
