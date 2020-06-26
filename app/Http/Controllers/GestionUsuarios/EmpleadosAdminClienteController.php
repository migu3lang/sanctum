<?php

namespace App\Http\Controllers\GestionUsuarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Administracion\Admincliente;
//use App\Events\NotificacionModulo;
use App\Administracion\AdminclienteModulo;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Administracion\EmpleadoCliente;

class EmpleadosAdminClienteController extends Controller
{
    public function index()
    {
        //$admincliente = new Admincliente;
        //$this->authorize('ver_admin', $admincliente);

    
       $credenciales=User::UserAuth(); 

      
    $empleados=User::select('users.id','users.name','users.apellido','users.email','empleado_clientes.cargo','empleado_clientes.estado')
                    ->join('empleado_clientes','empleado_clientes.user_id','=','users.id')
                    ->where('empleado_clientes.admincliente_id',$credenciales->admincliente_id)
                    ->where('empleado_clientes.estado','activo')
                    ->get();

     $roles=Role::where('admincliente_id',$credenciales->admincliente_id)->get();
    
       return view('empleadoCliente.index_empleados',compact('empleados','roles'));
    }

    public function create()
    {
        //$admincliente = new Admincliente;
        //$this->authorize('crear_admin', $admincliente);
        $user=User::UserAuth(); 
         $roles=Role::where('admincliente_id',$user->admincliente_id)->get();
        

        return view('empleadoCliente.create_empleados',compact('roles'));
    }

   
    public function store(Request $request)
    {
        //dd($request);
        $credenciales=User::UserAuth();
        
        $validatedData = $request->validate([
            'name' => 'required',
            'apellido' => 'required',
            'email'=> 'required|email',
            'cargo'=>'required',
            'roles'=>'required|array'],['name.required'=>'El campo nombre es requerido',
            'apellido.required'=>'El campo apellido es requerido',
            'email.required'=>'El email es requerido',
            'email.email'=>'debe ser un email']);
        
        $user=new User;
        $user->name=$request->name;
        $user->apellido=$request->apellido;
        $user->email=$request->email;
        $user->password=bcrypt('12345678');
        $user->save();

        $empleado=new EmpleadoCliente();
        $empleado->cargo=$request->cargo;
        $empleado->user_id=$user->id;
        $empleado->admincliente_id=$credenciales->admincliente_id;
        $empleado->save();

        $roles=Role::whereIn('id',$request->roles)->get();

        $user->assignRole($roles);

       // NotificacionModulo::dispatch($user);

        return response()->json('creado');   

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usuario=User::where('id',$id)->first();
        $roles=$usuario->roles()->get();
        $empleado=EmpleadoCliente::where('user_id',$id)->first();
        $array_role=[1];

        foreach($roles as $role)
        {array_push($array_role,$role->id);}

        $rolesLibres=Role::whereNotIn('id',$array_role)->get(); 

        return response()->json(['usuario'=>$usuario ,'empleado'=>$empleado ,'roles'=>$roles, "libres"=>$rolesLibres] );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $admincliente = new Admincliente;
        $this->authorize('editar_admin', $admincliente);

        $credenciales=User::CredencialAdmin(); 
        
        $validatedData = $request->validate([
            'name' => 'required',
            'apellido' => 'required',
            'email'=> 'required|email',
            'cargo'=>'required',
            'roles'=>'required|array'],['name.required'=>'El campo nombre es requerido',
            'apellido.required'=>'El campo apellido es requerido',
            'email.required'=>'El email es requerido',
            'email.email'=>'debe ser un email']);

            $comparador=$this->roleDisponible($credenciales->admincliente_id,$request->roles); //comparador de roles fuera de control
    
        if($comparador)
        {   
            $user=User::where('id',$id)->first();
            $empleado=EmpleadoCliente::where('user_id',$user->id)->first();
            
            $rolesActuales=$user->roles()->get();

            $user->name=$request->name;
            $user->apellido=$request->apellido;
            $user->email=$request->email;
            $user->update();

            $empleado->cargo=$request->cargo;
            $empleado->user_id=$user->id;
            $empleado->admincliente_id=$credenciales->admincliente_id;
            $empleado->update();

            $rolesRequest=Role::whereIn('id',$request->roles)->get();
            $user->syncRoles($rolesRequest);

            return back()->with('success','Se han actualizado los roles correctamente');
        }
         return back()->with('errores','hubo un problema en la sincronización de roles por favor intente nuevamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
            $credenciales=User::CredencialAdmin();
            $empleado=EmpleadoCliente::where('user_id',$user->id)
                                    ->where('admincliente_id',$credenciales->admincliente_id)
                                        ->first();

             $empleado->estado='desactivo';
             $empleado->update();

             $borrar=$user->roles()->get();

             $user->syncRoles([]);
            

        return back()->with('success','El empleado a sido desactivado');                        
             
    }

    public function roleDisponible($admincliente_id,$rolesRequest)
    {
        $roles=Role::where('admincliente_id',$admincliente_id)->get();
        
        $a=[];
        foreach($roles as $role){ array_push($a,$role->id);}
        $result=array_diff($rolesRequest,$a);


        return empty($result);
    }
}
