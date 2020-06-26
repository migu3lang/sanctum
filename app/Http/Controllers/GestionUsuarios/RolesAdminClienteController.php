<?php

namespace App\Http\Controllers\GestionUsuarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Administracion\Admincliente;
use App\Administracion\AdminclienteModulo;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\User;

class RolesAdminClienteController extends Controller
{
    public function index()
    {
        //$admincliente = new Admincliente;
        //$this->authorize('ver_roles', $admincliente);
        
        $user=User::UserAuth();

        $roles=Role::where('admincliente_id',$user->admincliente_id)->get();
        
        return view('rolesCliente.ver_role',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$admincliente = new Admincliente;
        //$this->authorize('crear_roles', $admincliente);

       
        $user=User::UserAuth();

        $modulos=AdminclienteModulo::join('modulos','modulos.id','=','admincliente_modulos.modulo_id')
                        ->select('modulos.nombreModulo')
                        ->where('admincliente_id',$user->admincliente_id)->get();
                                       
        $permisos=$this->permisosOrganizados($user->admincliente_id);

        
        //dd($permisos);
       

        return view('rolesCliente.crear_role',compact('modulos','permisos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //dd($request);
        $user=User::UserAuth();
        $validatedData = $request->validate([
            'name' => 'required', 'permisos'=> 'required|array'],
            ['name.required'=>'el campo nombre es requerido','roles.required'=>'Se debe asignar un permisos como mínimo',
            'roles.array'=> 'Alerta manipulacion de ingreso de información.']);

        $comparador=$this->comparadorPermisos($user,$request->permisos);
        
        if($comparador)
        {
            $permisos=Permission::whereIn('id',$request->permisos)->get();
            $role=Role::create(['name'=>$request->name,
                                'display_name'=>$request->name,
                                'admincliente_id'=>$user->admincliente_id]);

            $role->givePermissionTo($permisos);  
            return back()->with('success','Ha sido creado el rol');    
        } 
        
        return back()->with('errores','Hubo un problema con la sincronización de permisos vuelva a intentarlo');      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        // $admincliente = new Admincliente;
        // $this->authorize('editar_roles', $admincliente);

        $permisos=$this->permisosOrganizados($role->admincliente_id);
        $permisosActivos=$this->permisosRole($role->id);

      

        return view('rolesCliente.editar_role',compact('role','permisos','permisosActivos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        if($request->permisos == null)
        {
            return back()->with('errores','Hubo un problema de sincronización de permisos vuelva a intentarlo');
        }
        $user=User::UserAuth();
        $comparador=$this->comparadorPermisos($user,$request->permisos);
        //comparar si los permisos entrada request estan disponibles dentro los permisos por modulo asignado
        if($comparador)
        {
            $role->revokePermissionTo($role->permissions);
            $role->name=$request->name;
            $role->update();

            $role->givePermissionTo(Permission::whereIn('id',$request->permisos)->get());

            return redirect()->route('rolesAdmin.index')->with('success','el rol ha sido modificado');
        }   
            return back()->with('errores','Hubo un problema de sincronización de permisos vuelva a intentarlo');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return  response()->json('eliminado');

        
    }

    public function restore()
    {
        $user=User::UserAuth();

        $roles_eliminados = Role::onlyTrashed()
                ->where('admincliente_id',$user->admincliente_id)
                ->get();

                $roles_eliminados[0]->restore();

                return back()->with('success','El rol a sido reasignado');
    }


    
    //METODOS INTERNOS 


    // MODULOS ACTIVOS POR CLIENTE 
    public function moduloActivoPermisos(Admincliente $admincliente)
    {
         $activos=Permission::join('modulos','modulos.id','=','permissions.modulo_id')
                ->select('modulos.id','modulos.nombreModulo','permissions.name','permissions.id as permission_id')
                ->whereIn('modulo_id', DB::table('admincliente_modulos')->select('modulo_id')->where('admincliente_id',$admincliente->id))->get();

        return $activos;
    }

    //  METODO DEL SELECT PARA LLEVAR LOS MODULOS CON LOS PERMISOS
    public function permisosOrganizados($admincliente_id)
    {
        $modulosActivos=AdminclienteModulo::join('modulos','modulos.id','=','admincliente_modulos.modulo_id')
                            ->select('modulos.id','modulos.nombreModulo')
                            ->where('admincliente_modulos.admincliente_id',$admincliente_id)
                            ->get();
        $modulos=[];
            foreach($modulosActivos as $modulo)
            {
                $objectModulo = (object) array('id' => $modulo->id,'modulo'=>$modulo->nombreModulo, 'permisos'=> 'permisos');
                $modulos[]=$objectModulo; 
             }

            $arrayPermisos=[];
            foreach ($modulos as $modulo)
            {
                
                $permisoModulos=json_decode(Permission::where('modulo_id',$modulo->id)->get()->toJson());

                //dd($permisoModulos);
                foreach($permisoModulos as $key => $permiso)
                {    
                    $arrayPermisos['permiso'.$key]=(object)['permiso_id'=>$permiso->id, 'nombre'=>$permiso->name , 'display_name' => $permiso->display_name];

                    $modulo->permisos=(object)$arrayPermisos;
                }
                $arrayPermisos=[];
            }
         
            return $modulos;
    }


    // METODO DE LOS PERMISOS DE LOS ROLES
    public function permisosRole($role_id)
    {
        $permisos=DB::table('role_has_permissions')
                            ->select('permissions.id','permissions.name')
                            ->join('roles','roles.id','=','role_has_permissions.role_id')
                            ->join('permissions','permissions.id','=','permission_id')
                            ->where('roles.id',$role_id)
                            ->get();
                            
                            return $permisos;

    }

    public function comparadorPermisos($user,$requestPermisos)
    {
        $admincliente=Admincliente::where('id',$user->admincliente_id)->first();
        $a=[];
        $activos=$this->moduloActivoPermisos($admincliente);

         foreach($activos as $activo){array_push($a,$activo->permission_id);}
         $result=array_diff($requestPermisos,$a);

        return empty($result);
    }
}
