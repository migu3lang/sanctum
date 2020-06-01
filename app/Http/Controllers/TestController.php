<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Test;

class TestController extends Controller
{
    public function create( Request $request)
    {
        $test=new test();
        $test->name=$request->name;
        $test->save();

        return response()->json('pegelo');
    }

    public function index()
    {

        return response()->json(Test::all());
    }
}
