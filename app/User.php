<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use App\Administracion\Admincliente;
use Auth;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeUserAuth($query)
    {
        $query=$admincliente=Admincliente::select('users.id','users.name','users.email','adminclientes.id as admincliente_id','adminclientes.nombreAdmincliente')
        ->join('users','users.id','=','adminclientes.user_id')
        ->where('user_id',Auth::user()->id)->first();

        if(empty($admincliente->id))
        {
            $user=Auth::user();

            return $query=$user->roles()->first();
        }
      
        return $query;
    }
}