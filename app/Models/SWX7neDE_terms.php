<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SWX7neDE_terms extends Model
{
    protected $table = 'SWX7neDE_terms';
    protected $primaryKey = 'term_id';
    protected $fillable = [
        'name',
        'slug',
        'term_group'
    ];
}
