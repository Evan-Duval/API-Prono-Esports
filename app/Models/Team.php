<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'team_name',
        'photo_url',
    ];

    use HasFactory;
    public function jeux()
    {
        return $this->belongsToMany(Game::class, 'team_game');
    }
}
