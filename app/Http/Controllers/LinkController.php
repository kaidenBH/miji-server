<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\shortLink;

class LinkController extends Controller
{
    
    public function createLink(Request $request) {
        try {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_+=<>?';
            $code = substr(str_shuffle($characters), 0, 5);
            
            $existingshortLink = shortLink::where('short_link', $code)->first();
    
            while ($existingshortLink) {
                $code = substr(str_shuffle($characters), 0, 5);
                $existingshortLink = shortLink::where('short_link', $code)->first();
            }

            $validator = Validator::make($request->all(), [
                'original_link' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(["error" => $validator->errors()], 400);
            }
            $shortLink = new ShortLink;

            $shortLink->original_link = $request->input('original_link');
            $shortLink->short_link = $code;
            $shortLink->clicks_numer = 0;
            

            $shortLink->save();

            return response()->json(['short_link' => $code], 200);

        } catch (\Exception $e) {
            Log::error("Failed to create a short link: " . $e);
            return response()->json(['error' => $e], 401);
        }
    }

    public function getLink($short_link)
    {
        try{
            $shortLink = ShortLink::where('short_link', $short_link)->first();

            if (!$shortLink) {
                return response()->json(["error" => "Short link not found"], 404);
            }
    
            return response()->json(["original_link" => $shortLink->original_link], 200);
    
            
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "shortLink not found"], 404);    
        } catch (\Exception $e) {
            return response()->json(["error" => "Failed to delete shortLink"], 403);
        }      
    }

    public function destroy($short_link)
    {
        try{
            $shortLink = ShortLink::where('short_link', $short_link)->first();
            if (!$shortLink) {
                return response()->json(["error" => "Short link not found"], 404);
            }
            $shortLink->delete();
            
            return response()->json(["result" => "short link was deleted successfully"], 200); 
            
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "shortLink not found"], 404);    
        } catch (\Exception $e) {
            return response()->json(["error" => "Failed to delete shortLink"], 403);
        }      
    }
}
