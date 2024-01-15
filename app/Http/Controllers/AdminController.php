<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Hossam\Licht\Traits\ApiResponse;

class AdminController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $upcomingTests = Test::with('group.teams')->where('status', Test::COMMING)->get();
        $currentTests = Test::with('group.teams')->where('status', Test::CURRENT)->get();
        return view('admin.index', compact('currentTests', 'upcomingTests'));
    }
    public function UpdateTestsData($test)
    {
        $currentTests = Test::with('group.teams')->where('id', $test)->get();
        return $this->successResponse([
            'currentTests' => $currentTests,
        ]);
    }
}
