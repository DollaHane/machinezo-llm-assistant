<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ListingWorkHours extends Model
{
    /**
     * @var string
     */

    protected $table = 'listings_workhours';
    protected $fillable = ['listing_id', 'start', 'end', 'timezone'];
}
