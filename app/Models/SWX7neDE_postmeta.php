<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SWX7neDE_postmeta extends Model
{
    protected $table = 'SWX7neDE_postmeta';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'meta_key',
        'meta_value',
    ];

}
