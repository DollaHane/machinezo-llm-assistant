<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SWX7neDE_term_relationships extends Model
{
    protected $table = 'SWX7neDE_term_relationships';
    protected $primaryKey = 'object_id';
    protected $fillable = [
        'term_taxonomy_id',
        'term_order'
    ];
}
