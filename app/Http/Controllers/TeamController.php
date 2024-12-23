<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    /**
     * Afficher la liste des équipes
     * GET /api/teams/all
    */
    public function index(): JsonResponse
    {
        $teams = Team::All();
        return response()->json($teams, 200);
    }

    /**
     * Créer une nouvelle équipe
     * POST /api/teams/create
     */
    public function store(Request $request)
    {
        $request->validate([
            'team_name' => 'required|string|max:255',
            'photo_url' => 'required|url',
        ]);

        $photo_url = $request->photo_url;
        $context = stream_context_create(['http' => ['header' => "User-Agent: LaravelApp/1.0\r\n"]]);
        $imageData = file_get_contents($photo_url, false, $context);

        // Vérifier si le fichier est une image
        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($imageData);
        if (!in_array($mimeType, ['image/png', 'image/jpeg'])) {
            return response()->json(['error' => 'The file must be a PNG or JPEG image.'], 400);
        }

        $extension = $mimeType === 'image/png' ? 'png' : 'jpeg';
        $teamNameFileType = strtolower(str_replace(' ', '_', $request->team_name));

        $fileName = uniqid('team_' . $teamNameFileType . "_") . '.' . $extension;
        file_put_contents(base_path("storage/img/teams/{$fileName}"), $imageData);

        $team = Team::create(['team_name' => $request->team_name, 'photo_url' => $request->photo_url]);

        return response()->json($team, 201);
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
        $team = Team::findOrFail($id);
        $team->delete();
        return response()->json([],200);
    }
}
