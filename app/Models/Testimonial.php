<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    protected $table='Testimonial';
    protected $fillable = [

        'Name', 'Address', 'Image','Testimonial'
    ];
}
