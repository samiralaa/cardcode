<?php
namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardLink;
use Illuminate\Http\Request;

class CardLinkController extends Controller
{
    public function index()
    {
        // Get all card links
        return response()->json(CardLink::all());
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'card_id' => 'nullable',
            'link' => 'required|string',
            'logo' => 'required|string',
            'title' => 'required|string',
        ]);

        if (!$request->card_id) {
            $card = Card::create([
                'image' => 'add-profile-bigger.jpg',
                'qr_image' => 'image.png',
                'user_id' => auth()->user()->id,
            ]);
            $validated['card_id'] = $card->id;
        }

        // Create new card link
        $cardLink = CardLink::create($validated);

        return response()->json($cardLink, 201);
    }

    public function show($id)
    {
        // Get a single card link
        $cardLink = CardLink::findOrFail($id);
        return response()->json($cardLink);
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $validated = $request->validate([
            'card_id' => 'sometimes|exists:cards,id',
            'link' => 'sometimes|string',
            'logo' => 'sometimes|string',
            'title' => 'sometimes|string',
        ]);

        // Find card link and update
        $cardLink = CardLink::findOrFail($id);
        $cardLink->update($validated);

        return response()->json($cardLink);
    }

    public function destroy($id)
    {
        // Find card link and delete
        $cardLink = CardLink::findOrFail($id);
        $cardLink->delete();

        return response()->json(null, 204);
    }
}
