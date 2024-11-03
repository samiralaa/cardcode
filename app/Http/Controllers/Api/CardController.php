<?php

namespace App\Http\Controllers\API;

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

    public function update(Request $request, $id)
{
    $user = Auth::user();

    // Find the existing card record
    $card = Card::findOrFail($id);

    // Ensure the user is authorized to update this card
    if ($card->user_id !== $user->id) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Validate the request
    $validatedData = $request->validate([
        'image' => 'nullable|image',
        'qr_image' => 'nullable|image',
        'title_color' => 'nullable|string',
        'background_color' => 'nullable|string',
        'icon_color' => 'nullable|string',
        'share_color' =>'nullable|string'
    ]);

    // Handle the image upload
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($card->image) {
            \Storage::disk('public')->delete($card->image);
        }
        $imagePath = $request->file('image')->store('card_images', 'public');
        $validatedData['image'] = $imagePath;
    }

    if ($request->hasFile('qr_image')) {
        // Delete the old QR image if it exists
        if ($card->qr_image) {
            \Storage::disk('public')->delete($card->qr_image);
        }
        $imagePath = $request->file('qr_image')->store('qr_images', 'public');
        $validatedData['qr_image'] = $imagePath;
    }

    // Update the card
    $card->update($validatedData);

    return response()->json($card, 200); // Return the updated card
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
        $card = User::where('id', $user->id)->with('cards.cardLinks')->first();
        return response()->json($card, 200);
    }

    public function userCard($id)
    {
        $card = User::where('id', $id)->with('cards.cardLinks')->first();
        return response()->json($card, 200);
    }

    public function deleteImageCard($id){

        $card = Card::where('id', $id)->first();
       $card->image = null;
       $card->save();
        return response()->json($card, 200);
    }

    public function updateImageCard(Request $request, $id){
        $card = Card::where('id', $id)->first();
        $card->image = $request->image;
        $card->save();
        return response()->json($card, 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'qr_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title_color'=>'nullable',
            'background_color'=>'nullable',
            'icon_color'=>'nullable',
            'share_color'=>'nullable',
        ]);

        // Handle image upload for 'image' field
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        // Handle image upload for 'qr_image' field
        $qrImagePath = null;
        if ($request->hasFile('qr_image')) {
            $qrImage = $request->file('qr_image');
            $qrImageName = time() . '_' . $qrImage->getClientOriginalName();
            $qrImagePath = $qrImage->storeAs('qrcodes', $qrImageName, 'public');
        }

        // Create a new card instance and save it
        $card = new Card();
        $card->user_id = $user->id;
        $card->image = $imagePath; // Store the path to the image
        $card->qr_image = $qrImagePath; // Store the path to the QR code image
        $card->save();

        return response()->json($card, 200);
    }

}
