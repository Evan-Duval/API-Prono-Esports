<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeamGameController extends Controller
{

    /**
     * Récupérer la liste des liaisons entre Jeu et Equipe
     * GET api/jeux-equipes/all
     */
    public function index()
    {
        // Récupérer toutes les relations depuis la table pivot
        $relations = DB::table('team_game')
            ->join('games', 'team_game.game_id', '=', 'games.id')
            ->join('teams', 'team_game.team_id', '=', 'teams.id')
            ->select(
                'team_game.id',
                'games.id as game_id',
                'games.game_name as game_name',
                'teams.id as team_id',
                'teams.team_name as team_name'
            )
            ->get();

        return response()->json($relations);
    }

    /**
     * Créer une nouvelle liaison entre un Jeu et une Equipe
     * POST api/jeux-equipes/create
     */
    public function store(Request $request): JsonResponse    
{
    // Validation des données
    $validated = $request->validate([
        'game_id' => 'required|exists:games,id',
        'team_id' => 'required|exists:teams,id',
    ]);

    // Trouver le jeu et l'équipe
    $game = Game::find($validated['game_id']);
    $team = Team::find($validated['team_id']);

    // Relier l'équipe au jeu
    $game->equipes()->attach($team->id);

    // Retourner une réponse
    return response()->json($game->equipes, 201);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
