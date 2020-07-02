<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Administracion\Modulo;
use App\Administracion\AdminclienteModulo;
use App\Administracion\Admincliente;
use DB;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        $roles=$user->roles()->get();

        if($roles->count() == 0){
            $roles = [];
        }

        $admincliente=Admincliente::where('user_id',$user->id)->first();
        
        $modulos = [];

        if(!is_null($admincliente)){
            $modulos=AdminclienteModulo::where('admincliente_id',$admincliente->id)
            ->join('modulos','modulos.id','=','admincliente_modulos.modulo_id')
            ->select('modulos.nombreModulo')->get();
        }

        $token=$user->createToken($request->device_name)->plainTextToken;

        return response()->json(['token'=>$token , 'roles'=>$roles , 'modulos'=>$modulos]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }

}
