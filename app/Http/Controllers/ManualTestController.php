<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;

class ManualTestController extends Controller
{
    public function index(Test $test)
    {
        $test->load('group');
        return view('admin.current_manual_test',compact('test'));
    }
}
