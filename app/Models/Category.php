<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table='Categories';
    protected $primaryKey = 'CategoryId';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [

        'CategoryId', 'CategoryName', 'Description', 'Image','isMenu','IsActive','CreatedBy','StartingPrice' ,'parent_id','CategorySlug','CategoryTitle','Cities'

    ];
    
    public function parent()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Category', 'parent_id');
    }

    public function childrenRecursive()
    {
       return $this->children()->with('childrenRecursive');
    }
}
