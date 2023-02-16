<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class Equipment extends Model
{
    protected $table = 'equipment';
    protected $fillable = ['equipment','quantity','created_at','parent','rate','bill_of_qty_id','timestamp'];
}
