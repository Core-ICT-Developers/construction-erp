<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class Labour extends Model
{
    protected $table = 'labour';
    protected $fillable = ['labourer','number','bill_of_qty_id','timestamp','created_at','parent','rate'];
}
