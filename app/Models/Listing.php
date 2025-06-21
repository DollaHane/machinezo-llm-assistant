<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    /**
     * @var string
     * @return array<string, string>
     */

    protected $table = 'listings';
    protected $fillable = [
        'title',
        'description',
        'plant_category',
        'contact_email',
        'website',
        'phone_number',
        'hire_rate_pricing',
        'tags',
        'company_logo',
        'photo_gallery',
        'attachments',
        'social_networks',
        'location',
        'region',
        'related_listing',
        'hire_rental',
        'additional_1',
        'additional_2',
        'additional_3',
        'additional_4',
        'additional_5',
        'additional_6',
        'additional_7',
        'additional_8',
        'additional_9',
        'additional_10',
    ];
    protected $primaryKey = 'id';
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'photo_gallery' => 'array',
            'attachments' => 'array',
            'social_networks' => 'array',
            'related_listing' => 'array'
        ];
    }
}
