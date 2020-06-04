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

    public function dataTableInstitution(Request $request){

        $institutions = Institution::all();

        $columns = [
            [
                'label'=> 'Name',
                'field'=> 'name',
                'width'=> 150,
                'attributes'=> [
                'aria-controls'=> 'DataTable',
                'aria-label'=> 'Name',
                ],
            ],
            [
                'label'=> 'Position',
                'field'=> 'position',
                'width'=> 270,
            ],
            [
                'label'=> 'Office',
                'field'=> 'office',
                'width'=> 200,
            ],
            [
                'label'=> 'Age',
                'field'=> 'age',
                'sort'=> 'asc',
                'width'=> 100,
            ],
            [
                'label'=> 'Start date',
                'field'=> 'date',
                'sort'=> 'disabled',
                'width'=> 150,
            ],
            [
                'label'=> 'Salary',
                'field'=> 'salary',
                'sort'=> 'disabled',
                'width'=> 100,
            ]
        ];
        
        $rows= [
            [
                'name'=> 'Tiger Nixon',
                'position'=> 'System Architect',
                'office'=> 'Edinburgh',
                'age'=> 61,
                'date'=> '2011/04/25',
                'salary'=> '$320',
            ],
            [
                'name'=> 'Garrett Winters',
                'position'=> 'Accountant',
                'office'=> 'Tokyo',
                'age'=> 63,
                'date'=> '2011/07/25',
                'salary'=> '$170',
            ],
            [
                'name'=> 'Ashton Cox',
                'position'=> 'Junior Technical Author',
                'office'=> 'San Francisco',
                'age'=> 66,
                'date'=> '2009/01/12',
                'salary'=> '$86',
            ],
            [
                'name'=> 'Cedric Kelly',
                'position'=> 'Senior Javascript Developer',
                'office'=> 'Edinburgh',
                'age'=> 22,
                'date'=> '2012/03/29',
                'salary'=> '$433',
            ],
            [
                'name'=> 'Tiger Nixon',
                'position'=> 'System Architect',
                'office'=> 'Edinburgh',
                'age'=> 61,
                'date'=> '2011/04/25',
                'salary'=> '$320',
            ],
            [
                'name'=> 'Airi Satou',
                'position'=> 'Accountant',
                'office'=> 'Tokyo',
                'age'=> 33,
                'date'=> '2008/11/28',
                'salary'=> '$162',
            ],
            [
                'name'=> 'Brielle Williamson',
                'position'=> 'Integration Specialist',
                'office'=> 'New York',
                'age'=> 61,
                'date'=> '2012/12/02',
                'salary'=> '$320',
            ]
        ];

        $dataTable = [
            'columns'=>$columns,
            'rows'=> $rows
        ];
        return response()->json(['dataTable'=>$dataTable],200);
    }
}
