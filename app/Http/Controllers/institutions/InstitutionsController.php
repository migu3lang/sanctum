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

    public function getAllInstitutions(){

        $institutions = Institution::all();

        return response()->json(['institutions'=>$institutions],200);
    }

    public function getInstitution(Request $request){

        $institution = Institution::find($request->idInstitution);
        
        return response()->json(['institution'=>$institution],200);
    }
}
