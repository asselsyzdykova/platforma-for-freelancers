<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        return response()->json(
            News::where('is_active', '1')
                ->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        return News::create($validated);
    }

    public function destroy(string $id){
        $news = News::findOrFail($id);
        $news -> delete();
        return response()->json(['message' => 'new deleted successfully']);
    }
}
