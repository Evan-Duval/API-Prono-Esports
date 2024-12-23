<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    /**
     * Afficher la liste des jeux.
     * GET /api/games/all
     */
    public function index(): JsonResponse
    {
        $games = Game::All();
        return response()->json($games, 200);
    }

    /**
     * Créer un nouveau joueur
     * POST /api/games/create
     */
    public function store(Request $request): JsonResponse
    {
        $game = Game::create(
            $request->validate([
                'game_name' => 'required|string|max:255',
            ])
        );
        return response()->json($game, 201);
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
    public function destroy(string $id): JsonResponse
    {
        $game = Game::findOrFail($id);
        $game->delete();
        return response()->json([],200);
    }
}
