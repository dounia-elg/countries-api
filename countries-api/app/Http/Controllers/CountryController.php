<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    /**
     * Afficher la liste de tous les pays.
     */
    public function index()
    {
        $countries = Country::all();
        return response()->json($countries);
    }

    /**
     * Enregistrer un nouveau pays.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:countries',
            'capital' => 'required|string|max:255',
            'population' => 'required|numeric|min:0',
            'region' => 'required|string|max:255',
            'flag_url' => 'nullable|string|max:255',
            'motto' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $country = Country::create($request->all());
        return response()->json($country, 201);
    }

    /**
     * Afficher les détails d'un pays spécifique.
     */
    public function show($id)
    {
        $country = Country::find($id);
        
        if (!$country) {
            return response()->json(['message' => 'Pays non trouvé'], 404);
        }
        
        return response()->json($country);
    }

    /**
     * Mettre à jour un pays existant.
     */
    public function update(Request $request, $id)
    {
        $country = Country::find($id);
        
        if (!$country) {
            return response()->json(['message' => 'Pays non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:countries,name,' . $id,
            'capital' => 'string|max:255',
            'population' => 'numeric|min:0',
            'region' => 'string|max:255',
            'flag_url' => 'nullable|string|max:255',
            'motto' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $country->update($request->all());
        return response()->json($country);
    }

    /**
     * Supprimer un pays.
     */
    public function destroy($id)
    {
        $country = Country::find($id);
        
        if (!$country) {
            return response()->json(['message' => 'Pays non trouvé'], 404);
        }
        
        $country->delete();
        return response()->json(['message' => 'Pays supprimé avec succès']);
    }
    
    /**
     * Upload ou mise à jour du drapeau d'un pays.
     */
    public function updateFlag(Request $request, $id)
    {
        $country = Country::find($id);
        
        if (!$country) {
            return response()->json(['message' => 'Pays non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'flag' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Supprimer l'ancien drapeau s'il existe
        if ($country->flag_url && Storage::exists('public/flags/' . basename($country->flag_url))) {
            Storage::delete('public/flags/' . basename($country->flag_url));
        }

        // Enregistrer le nouveau drapeau
        $flagPath = $request->file('flag')->store('public/flags');
        $country->flag_url = Storage::url($flagPath);
        $country->save();

        return response()->json([
            'message' => 'Drapeau mis à jour avec succès',
            'flag_url' => $country->flag_url
        ]);
    }
    
    /**
     * Récupérer le drapeau d'un pays.
     */
    public function getFlag($id)
    {
        $country = Country::find($id);
        
        if (!$country || !$country->flag_url) {
            return response()->json(['message' => 'Drapeau non trouvé'], 404);
        }
        
        return response()->json(['flag_url' => $country->flag_url]);
    }
}