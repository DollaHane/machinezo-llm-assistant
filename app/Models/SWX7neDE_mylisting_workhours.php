<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SWX7neDE_mylisting_workhours extends Model
{
    protected $table = 'listings_workhours';
    protected $primaryKey = 'id';
    protected $fillable = ['listing_id', 'start', 'end', 'timezone'];
}
