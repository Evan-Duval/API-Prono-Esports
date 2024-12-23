<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    /**
     * Afficher la liste des joueurs
     * GET /api/players/all
    */
    public function index(): JsonResponse
    {
        $players = Player::All();
        return response()->json($players, 200);
    }

    /**
     * Créer un nouveau joueur
     * POST /api/players/create
     */
    public function store(Request $request): JsonResponse
    {
        $player = Player::create(
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'username'=> 'required|string|max:255',
                'team_id' => 'integer|max:255',
            ])
        );
        return response()->json($player, 201);
    }

    /**
     * Afficher un joueur spécifique
     * GET /api/players/{id}
     */
    public function show(string $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        return response()->json($player, 200);
    }

    /**
     * Mettre à jour un joueur
     * PUT /api/players/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        $player->update(
            $request->validate([
                'first_name' =>'required|string|max:255',
                'last_name' =>'required|string|max:255',
                'username'=> 'required|string|max:255',
                'team_id' => 'integer|max:255',
            ])
        );
        return response()->json($player, 202);
    }

    /**
     * Supprimer un joueur
     * DELETE /api/players/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        $player->delete();
        return response()->json([],200);
    }

    /**
     * Rechercher des joueurs
     * GET /api/players/search
     */
    public function search(Request $request): JsonResponse
    {
        $query = Player::query();
        // Recherche par nom/prénom
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('username', 'like', "%{$searchTerm}%");
            });
        }

        // Filtres
        if ($request->has('first_name')) {
            $query->where('first_name', 'like', "%{$request->first_name}%");
        }

        if ($request->has('last_name')) {
            $query->where('last_name', 'like', "%{$request->last_name}%");
        }

        if ($request->has("username")) {
            $query->where('username', 'like', "%{$request->username}%");
        }

        // Tri
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $players = $query->paginate($request->input('per_page', 10));

        return response()->json($players,200);
    }
}
