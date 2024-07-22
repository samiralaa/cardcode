<?php

namespace App\Http\Controllers\Api;

use App\Models\Card;
use App\Models\User;
use App\Models\CardLink;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function index()
    {
        return response()->json(Card::all(), 200);
    }

    public function show($slug)
    {

     
        $card =User::where('name', $slug)->with('cards.cardLinks')->first();
      
    
        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }
        return response()->json($card, 200);
    }

  
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'logo' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'card_links' => 'required|array',
            'card_logo' => 'required|array',
            'card_title' => 'required|array',
            'card_links.*' => 'required|string|max:255',
            'card_logo.*' => 'nullable|string|max:255',
            'card_title.*' => 'nullable|string|max:255',
        ]);

        $validatedData['user_id'] = $user->id;
        $validatedData['slug'] = $user->name;

        // Handle the image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('card_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        // Create the Card
        $card = Card::create($validatedData);

        // Ensure arrays are properly handled
        $cardLinks = $request->input('card_links', []);
        $cardLogos = $request->input('card_logo', []);
        $cardTitles = $request->input('card_title', []);

        // Validate lengths match
        if (count($cardLinks) !== count($cardLogos) || count($cardLinks) !== count($cardTitles)) {
            return response()->json(['message' => 'Mismatch in card_links, card_logo, or card_title counts'], 400);
        }

        // Create the associated CardLinks
        foreach ($cardLinks as $index => $link) {
            CardLink::create([
                'card_id' => $card->id,
                'link' => $link,
                'logo' => $cardLogos[$index] ?? null,
                'title' => $cardTitles[$index] ?? null,
            ]);
        }

        return response()->json($card->load('cardLinks'), 201); // Load card links in response
    }
    
 public function update(Request $request, $id)
{
    $user = Auth::user();
    $card = Card::findOrFail($id);

    // Ensure the card belongs to the authenticated user
    if ($card->user_id !== $user->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validatedData = $request->validate([
        'title' => 'sometimes|required|string|max:255',
        'link' => 'sometimes|required|string|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($request->hasFile('logo')) {
        // Optionally delete the old logo if it exists
        if ($card->logo) {
            Storage::disk('public')->delete($card->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $validatedData['logo'] = $path;
    }

    $validatedData['slug'] = $user->name;

    // Update the card with the validated data
    $card->update($validatedData);

    return response()->json($card, 200);
}

    
    public function destroy($id)
    {
        $card = Card::find($id);
        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        if ($card->logo) {
            Storage::disk('public')->delete($card->logo);
        }

        $card->delete();

        return response()->json(['message' => 'Card deleted'], 200);
    }
}
