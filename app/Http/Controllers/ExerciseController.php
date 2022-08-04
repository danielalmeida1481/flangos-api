<?php

namespace App\Http\Controllers;

use App\Http\Traits\CategoryTrait;
use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    use CategoryTrait;

    /**
     * Get exercises
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request, $id = null)
    {
        if (!$id) {
            return response(
                $request->user()
                    ->exercises()
                    ->with('category:id,name')
                    ->get(['id', 'name', 'category_id'])
                    ->makeHidden('category_id'),
                200
            );
        }

        $exercise = $request->user()
            ->exercises()
            ->whereId($id)
            ->with('category:id,name')
            ->first(['id', 'name', 'category_id']);

        if (!$exercise) {
            return response([], 404);
        }

        return response($exercise->makeHidden('category_id'), 200);
    }

    /**
     * Store exercise
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'integer|nullable',
            'category_name' => 'string|max:255|nullable'
        ]);

        $userId = $request->user()->id;

        /**
         * Use an already existing category
         */
        $category = Category::whereId($validated['category_id'])
            ->whereUserId($userId)
            ->first();

        if ($category) {
            Exercise::create([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'user_id' => $userId
            ]);

            return response(['message' => "Exercise created successfully."], 200);
        }

        /**
         * Create new category
         */
        if (!empty($validated['category_name'])) {
            $category = Category::whereName($validated['category_name'])
                ->whereUserId($userId)
                ->first();

            if ($category) {
                return response([
                    'errors' => [
                        'category_name' => [
                            "A category with that name already exists!"
                        ]
                    ]
                ], 422);
            }

            $category = Category::create([
                'name' => $validated['category_name'],
                'user_id' => $request->user()->id
            ]);

            Exercise::create([
                'name' => $validated['name'],
                'category_id' => $category->id,
                'user_id' => $userId
            ]);

            return response(['message' => "Exercise created successfully."], 200);
        }

        /**
         * No category
         */
        Exercise::create([
            'name' => $validated['name'],
            'category_id' => NULL,
            'user_id' => $userId
        ]);

        return response(['message' => "Exercise created successfully."], 200);
    }

    /**
     * Update exercise
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'integer|nullable',
            'category_name' => 'string|max:255|nullable'
        ]);

        $exercise = Exercise::whereId($id)
            ->first();

        if (!$exercise) {
            return response(['message' => "No exercise found."], 404);
        }

        $exercise->name = $validated['name'];
        $userId = $request->user()->id;
        $oldCategoryId = $exercise->category_id;

        /**
         * Use an already existing category
         */
        $category = Category::whereId($validated['category_id'])
            ->whereUserId($userId)
            ->first();

        if ($category) {
            $exercise->category_id = $validated['category_id'];
            $exercise->update();
        } else if (!empty($validated['category_name'])) {
            /**
             * Create new category
             */
            $category = Category::whereName($validated['category_name'])
                ->whereUserId($userId)
                ->first();

            if ($category) {
                return response([
                    'errors' => [
                        'category_name' => [
                            "A category with that name already exists!"
                        ]
                    ]
                ], 422);
            }

            $category = Category::create([
                'name' => $validated['category_name'],
                'user_id' => $request->user()->id
            ]);

            $exercise->category_id = $category->id;
            $exercise->update();
        } else {
            /**
             * No category
             */
            $exercise->category_id = NULL;
            $exercise->update();
        }

        /**
         * Delete of category if exercises count === 0
         */
        if ($oldCategoryId) {
            $this->deleteCategoryIfZeroExercises($oldCategoryId);
        }

        return response(['message' => "Exercise updated successfully."], 200);
    }

    /**
     * Delete exercise
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        $exercise = $request->user()
            ->exercises()
            ->whereId($id)
            ->with('category')
            ->first();

        if (!$exercise) {
            return response([], 404);
        }

        $exercise->delete();
        if ($exercise->category) {
            $this->deleteCategoryIfZeroExercises($exercise->category->id);
        }

        return response(['message' => "Exercise deleted successfully."], 200);
    }

}
