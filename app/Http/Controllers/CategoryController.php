<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json(['status' => 200, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return response()->json(['status' => 200, 'success' => true, 'message' => 'Pomyślnie dodano nową kategorie!']);
    }

    public function update(Request $request)
    {
        $validation = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->update();

        return response()->json(['status' => 200, 'success' => true, 'message' => 'Pomyślnie aktualizowano kategorie!']);
    }

    public function destroy(Request $request)
    {
        Category::destroy($request->id);

        return response()->json(['status' => 200, 'success' => true, 'message' => 'Pomyślnie usunięto kategorie!']);
    }

    public function archive()
    {
        $categories = Category::onlyTrashed()->get();

        return response()->json(['categories' => $categories]);
    }

    public function forceDelete(Request $request)
    {
        $category = Category::onlyTrashed()->find($request->id);
        $category->forceDelete();

        return response()->json(['status' => 200, 'success' => true, 'message' => "Pomyślnie usunięto kategorie!"]);
    }
}
