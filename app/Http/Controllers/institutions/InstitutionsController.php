<?php

namespace App\Http\Controllers\institutions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\institutions\Institution;

class InstitutionsController extends Controller
{
    public function newInstitution(Request $request){

        $request->validate([
            'institutionName' => ['required'],
            'institutionInfo' => ['required']
        ]);

        $newInstitution = new Institution();
        $newInstitution->institutionName = $request->institutionName;
        $newInstitution->institutionInfo = $request->institutionInfo;
        $newInstitution->save();

        return response()->json("successful",200);
    }
}
