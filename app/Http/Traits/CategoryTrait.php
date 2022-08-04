<?php

namespace App\Http\Traits;

use App\Models\Category;

trait CategoryTrait {

    /**
     * Delete a category if the number of exercises is zero
     *
     * @param int $category_id
     * @return void
     */
    protected function deleteCategoryIfZeroExercises($category_id)
    {
        $category = Category::whereId($category_id)
            ->withCount('exercises')
            ->first();

        if (!$category) {
            return;
        }

        if ($category->exercises_count === 0) {
            $category->delete();
        }
    }

}
