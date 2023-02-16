<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class MaterialsUsed extends Model
{
    protected $table = 'materials_used';
    protected $fillable = ['description', 'unit','quantity','timestamp','created_at','parent','price','bill_of_qty_id'];
}
