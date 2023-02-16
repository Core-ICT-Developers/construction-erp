<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class BOQLevels extends Model
{
    protected $table = 'boq_levels';
    protected $fillable = ['title','parent','boq_id','cell','updated_at','created_at','level'];
}
