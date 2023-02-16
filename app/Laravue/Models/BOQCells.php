<?php
namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;


class BOQCells extends Model
{
    protected $table = 'boq_cells';
    protected $fillable = ['cell','parent','boq_id','updated_at','created_at','title','unit','quantity','rate','quantity_done','cost_of_work','value_of_work','profit_loss'];
}
