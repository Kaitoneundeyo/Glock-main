<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    $category= Category::all();
    return view('kategori.index', compact('category'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategori.form_kt');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug|max:255',
        ]);


        Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }




    /**
     * Display the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('kategori.edit_kt', compact('category'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    $category = Category::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:Categories,slug,' . $category->id,
    ]);

    $category->update([
        'name' => $request->name,
        'slug' => $request->slug,
    ]);

    return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
    $category = Category::findOrFail($id);
    $category->delete();

    return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

}
