<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class ProjectManagement extends Model
{
    protected $table = 'projectmanagement';
    protected $fillable = ['durationstep','starttime','endtime','projectname','startdate','templates','workingdays','open','progress','planned_start','planned_end','parent','duration','created_at','updated_at'];
}
