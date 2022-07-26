<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * Get categories
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request, $id = null) {
        if (!$id) {
            return response(
                $request->user()
                    ->categories()
                    ->get(['id', 'name'])
                , 200);
        }

        $category = $request->user()
            ->categories()
            ->whereId($id)
            ->first(['id', 'name']);

        if (!$category) {
            return response([], 404);
        }

        return response($category, 200);
    }

    /**
     * Store category
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255'
        ]);

        $category = Category::whereName($validated['name'])
            ->whereUserId($request->user()->id)
            ->first();

        if ($category) {
            return response([
                'errors' => [
                    'name' => [
                        "A category with that name already exists!"
                    ]
                ]
            ], 422);
        }

        Category::create([
            'name' => $validated['name'],
            'user_id' => $request->user()->id
        ]);

        return response(['message' => "Category created successfully."], 200);
    }

}
