<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function index()
    {
        return response()->json(Card::all(), 200);
    }

    public function getOne($id)
    {
        $card = Card::where('id', $id)->with('cardLinks')->first();
        return response()->json($card);
    }

    public function show($slug)
    {

        $card = User::where('name', $slug)->with('cards.cardLinks')->first();

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
            'image' => 'required|image',
            'qr_image' => 'nullable|image',
        ]);
        $validatedData['user_id'] = $user->id;

        // Handle the image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('card_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        if ($request->hasFile('qr_image')) {
            $imagePath = $request->file('image')->store('qr_image', 'public');
            $validatedData['qr_image'] = $imagePath;
        }
        // Create the Card
        $card = Card::create($validatedData);
        return response()->json($card, 201); // Load card links in response
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

    public function myCard()
    {
        $user = auth()->user();
        $card = Card::where('user_id', $user->id)->with('cards.cardLinks')->first();
        return response()->json($card, 200);
    }
}
