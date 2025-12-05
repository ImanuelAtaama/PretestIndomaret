<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';
    protected $fillable = ['id_role', 'username', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    public $timestamps = false;

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }
}
