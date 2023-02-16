<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class WorkDone extends Model
{
    protected $table = 'work_done';
    protected $fillable = ['unit','quantity','timestamp','created_at','parent','rate','bill_of_qty_id'];
}
