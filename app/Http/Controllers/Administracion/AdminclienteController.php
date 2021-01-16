<?php

namespace App\Http\Controllers\Administracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Administracion\Admincliente;
use App\Administracion\Modulo;
use App\Administracion\AdminclienteModulo;
use App\Administracion\Historialpermiso;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Administracion\EmpleadoCliente;
use App\ParametricasRunt\Organismos;


class AdminclienteController extends Controller
{
   
    public function index()
    {
        $admincliente = new Admincliente;

        $adminclientes=Admincliente::select('adminclientes.id','users.name','users.email','adminclientes.nombreAdmincliente','adminclientes.telefono')
                                    ->join('users','users.id','=','adminclientes.user_id')
                                    ->whereNotIn('adminclientes.id',[1])
                                    ->get();

        return response()->json(['clients'=>$adminclientes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$admincliente = new Admincliente;
        //$this->authorize('crear_cliente', $admincliente);
       

        return view('admincliente.create_cliente');
    }

    // METODO PARA CREAR NUEVOS CLIENTES
    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required'],
            'email' => ['required','email'],
            'telefono'=>['required','numeric']
        ]);
        
        $user=new user();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=bcrypt('12345678');
        
        $user->save();

        $admincliente=new Admincliente();
        $admincliente->nombreAdmincliente=$user->name."Administrador";
        $admincliente->user_id=$user->id;
        $admincliente->telefono=$request->telefono;
        $admincliente->save();  

        

        $empleado_cliente=new EmpleadoCliente();
        $empleado_cliente->user_id= $user->id;
        $empleado_cliente->admincliente_id = $admincliente->id;
        $empleado_cliente->cargo="Administrdor General";
        $empleado_cliente->save();

        $modulo1=new AdminclienteModulo();
        $modulo1->admincliente_id=$admincliente->id;
        $modulo1->modulo_id=2;
        $modulo1->save();


        $permisos=Permission::whereIn('modulo_id',[2])->get();
        $roleEmpalme = Role::create(['name' => $admincliente->nombreAdmincliente ,'display_name'=>'Administrador']);
        $roleEmpalme->givePermissionTo($permisos);


        $user->assignRole($roleEmpalme);
        $roleEmpalme->update(['admincliente_id'=>$admincliente->id]);



        return response()->json('ya');

    }

    // PARA IR AL PANEL LISTADO DE MODULO Y HABILITAR O QUITAR LOS MODULO DISPONIBLES
    public function adminCliente_modulos(Admincliente $admincliente)
    {
        // $this->authorize('ver_cliente', $admincliente);
        // se excluye el modulo de super administrador y el de Administrador ya que se manera previa se carga
        $modulos=Modulo::where('id','!=',1)
                            ->get();


        $moduloActivos=$this->modulosActivos($admincliente);

        //dd($modulos,$moduloActivos);
        return response()->json(['modulos'=>$modulos,'modulosActivos'=>$moduloActivos, "cliente"=>$admincliente]);
        //return view('admincliente.asignar_modulo_clientes',compact('modulos','admincliente','moduloActivos'));
    }


    // METODO QUE HACE TODO EL PROCESO DE ELIMINACION PERMISOS DEPENDIENDO DE LOS MODULOS ACTIVOS    
    public function admincliente_storemodulos(Admincliente $admincliente,Request $request)
    {

        $modulosViejos=$this->modulosActivos($admincliente);
        
        //return response()->json($request->modules);

            if($request->modules)
            {
        
                $arrayModulosViejos=[];
                if(count($modulosViejos) == count($request->modules))
                {
                foreach($modulosViejos as $modulo){array_push($arrayModulosViejos,$modulo->modulo_id); }  

                $result=array_diff($request->modules,$arrayModulosViejos);

                if(empty($result))
                {
                    // error de react
                    return response()->json(['mensaje'=>'error']);
                }
                }
                    
                        $role=Role::where('admincliente_id',$admincliente->id)->first();
                        //dd($role);
                        $anteriores=$this->moduloActivoPermisos($admincliente);
                        $role->revokePermissionTo($anteriores);

                        $sync=DB::table('admincliente_modulos')
                        ->where('admincliente_id',$admincliente->id)
                        ->delete();  
                        
                        foreach($request->modules as $modulo){
                            $adminclientemodulo=new AdminclienteModulo();
                            $adminclientemodulo->admincliente_id=$admincliente->id;
                            $adminclientemodulo->modulo_id=$modulo;
                            $adminclientemodulo->save();
                        }
                        $modulosNuevos=$this->modulosActivos($admincliente);

                        $activos=$this->moduloActivoPermisos($admincliente);
                        $role->givePermissionTo($activos);

                        if(count($modulosViejos) > count($modulosNuevos))
                        {  
                            $comparador=$this->comparadorModulos($modulosViejos,$modulosNuevos);
                            $roles=$this->softDeleterole($comparador,$admincliente->id);
                        
                            return response()->json(['mensaje'=>'cambios']);
                            
                        }elseif(count($modulosViejos) < count($modulosNuevos)){

                            $comparador=$this->comparadorModulos($modulosNuevos,$modulosViejos);
                            $restore=$this->restoreRolepermisos($comparador,$admincliente->id);
                        
                        // dd($restore);
                        return response()->json(['mensaje'=>'cambios']);
                        } 
                        elseif(count($modulosViejos) == count($modulosNuevos))
                        {
                            //dd($modulosViejos->toArray(),$modulosNuevos->toArray());
                            //$comparador=$this->comparadorModulos($modulosNuevos,$modulosViejos);
                            $arrayA=[];
                            $arrayB=[];
                            for($i=0 ;$i<count($modulosNuevos); $i++ )
                            {
                                array_push($arrayA,$modulosViejos[$i]->modulo_id);
                                array_push($arrayB,$modulosNuevos[$i]->modulo_id);
                            }
                            $result=array_diff($arrayA,$arrayB);
                            $this->softDeleterole($result,$admincliente->id);

                                
                            $permisos=Historialpermiso::whereIn('modulo_id',$arrayB)     
                            ->where('admincliente_id',$adminclientemodulo->admincliente_id)->get();

                            if(count($permisos)>0)
                            {
                                $this->restoreRolepermisos($arrayB,$admincliente->id);

                            }

                            return response()->json('ya');
                        }
                    } 
            

            return response()->json(['mensaje'=>'hola']);
    }




        // METODOS INTERNOS DE BUSQUEDA 
        // METODO CONSULTA EXISTENCIA DE MODULOS 
    public function modulosActivos(Admincliente $admincliente)
    {
        $activos=AdminclienteModulo::where('admincliente_id',$admincliente->id)->get();

        return $activos;
    }

    public function moduloActivoPermisos(Admincliente $admincliente)
    {
        //  $activos=Permission::whereIn('modulo_id', DB::table('admincliente_modulos')
        //  ->select('modulo_id')
        //  ->where('admincliente_id',$admincliente->id))->get();

            $activos=AdminclienteModulo::where('admincliente_id',$admincliente->id)->get();

    
        return $activos;
    }

    public function comparadorModulos($anteriores,$posteriores)
    {

        $arrayAnterior=[];
        $arrayPosterior=[];
        $arraySalida=[];
        $arryaSalidaParse=[];
  
    foreach($anteriores as $anterior)
    {array_push($arrayAnterior,$anterior->modulo_id);}
    foreach($posteriores as $posterior)
    {array_push($arrayPosterior,$posterior->modulo_id);}

    if(count($arrayAnterior)>count($arrayPosterior))
        {
            $arraySalida=$arrayAnterior;
        
            for($i=0; $i <count($arrayAnterior) ; $i++)
            {
                for($j=0; $j<count($arrayPosterior) ; $j++)
                {
                    if($arrayAnterior[$i] == $arrayPosterior[$j])
                    {
                       unset($arraySalida[$i]);
                    }
                }
            }
            foreach($arraySalida as $salida){
                array_push($arryaSalidaParse,$salida);
            }
            
            return $arryaSalidaParse; // quita el modulo y elmina los roles que estan asociaddo ha esos modulo y permisos
        }   

    }


    public function restoreRolepermisos($modulos,$admincliente_id){
        
        
        $permisos=Historialpermiso::whereIn('modulo_id',$modulos)     
                                    ->where('admincliente_id',$admincliente_id)->get();
          
        if(count($permisos) == 0){
                return "Se han creado nuevos permisos"; }

                for($i=0 ; $i<count($permisos); $i++)
                {
                    $rol=Role::where('id',$permisos[$i]->role_id)->first();
                    $permisou=Permission::where('id',$permisos[$i]->permission_id)->first();
                    $rol->givePermissionTo($permisou->name);
                }

                    return "lo logramos";
        }


    // eliminar permisos a los roles cuyos modulos hallan sido desconectados.
    public function softDeleterole($modulos,$admincliente_id)
    {
        $salida=[];
        $permisos=Permission::whereIn('modulo_id',$modulos)->get();

        $roleFinder=DB::table('role_has_permissions')
        ->select('roles.id')
        ->join('roles','roles.id','=','role_has_permissions.role_id')                   
        ->join('permissions','permissions.id','=','role_has_permissions.permission_id')
        ->whereIn('role_has_permissions.permission_id',DB::table('permissions')->select('permissions.id')->whereIn('modulo_id',$modulos))
        ->where('roles.admincliente_id',$admincliente_id)
        ->get();

       // dd($roleFinder);

         foreach($roleFinder as $key => $role )
           { $salida[$key]=$role->id;
            }
            $salida_parse=array_unique($salida);
        foreach($salida_parse as $id)
        {
            $aux=Role::where('id',$id)->first();
            $permisosActivos=$aux->permissions()->get();

            // aqui se hace la intercepecion entre los permisos que tiene el rol con todos los del modulo
            $interseccion=$permisos->intersect($permisosActivos);

            $aux->revokePermissionTo($permisos);

            foreach($interseccion as $permiso){
                $guardar=new Historialpermiso();
                $guardar->admincliente_id=$admincliente_id;
                $guardar->role_id=$aux->id;
                $guardar->permission_id=$permiso->id;
                $guardar->modulo_id=$permiso->modulo_id;
                $guardar->save();
            }
        }


              return $salida; 
    }


    public function editClient(Admincliente $admincliente, Request $request){

        $user=User::where('id',$admincliente->user_id)->first();

        $user->name=$request->name;
        $user->email=$request->email;
        $admincliente->telefono=$request->telefono;
        $user->update();
        $admincliente->update();

    }

     
}
