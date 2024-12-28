<?php

namespace App\Models;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'setting_name' => 'string',
        'setting_value' => 'string',
    ];

    // Relation to the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
