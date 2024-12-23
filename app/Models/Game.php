<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'game_name',
    ];

    use HasFactory;
    public function equipes()
    {
        return $this->belongsToMany(Team::class, 'team_game');
    }
}
