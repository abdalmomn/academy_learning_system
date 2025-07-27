<?php

        namespace App\Services;
        use App\Models\Category;
        use App\DTO\CategoryDto;
        use Illuminate\Support\Facades\DB;
        use Illuminate\Support\Facades\Log;
        use Exception;
        class CategoryService
        {
            public function getAll()  //get all categories
            {
                try {
                    $categories = Category::all();
                    if($categories->isEmpty()){   //check if categories is found
                    return [
                        'data' => null,
                        'message' => 'not found categories'
                    ];
                    }else{
                        return [
                            'data' => $categories,
                            'message' => 'All categories'
                        ];
                    }
                } catch (\Exception $e) {   //save failed get all in log file
                    Log::error('Fetching categories failed', ['error' => $e->getMessage()]);
                    return [
                        'data' => null,
                        'message' => 'Failed to fetch categories'
                    ];
                }
            }

            public function getById($id)    //get one category with details
            {
                try {
                    $category = Category::find($id);    //check if categories is found
                    if(!$category) {
                        return [
                            'data' => null,
                            'message' => 'this Category not found'
                        ];
                    }else{
                        return [
                            'data' => $category,
                            'message' => 'Category details'
                        ];
                    }
                } catch (\Exception $e) {
                    // save failed get by id in log file
                    Log::error('Fetching category failed', [
                        'error' => $e->getMessage(),
                        'id' => $id
                    ]);
                    return [
                        'data' => null,
                        'message' => 'Failed to fetch category'
                    ];
                }
            }

            public function store(CategoryDto $dto)
            {
                //check for permission role
                // check foe the user must be found and must be admin
                $user = auth()->user();
                if (!$user || auth()->user()->role !== 'admin') {
                    return [
                        'data' => null,
                        'message' => 'Unauthorized - admin only'
                    ];
                }
                DB::beginTransaction();
                try {
                    $category = Category::query()->create([
                        'category_name' => $dto->category_name
                    ]);

                    DB::commit();
                    // save in log file
                    Log::info('Category created', ['category_name' => $dto->category_name]);

                    return [
                        'data' => $category,
                        'message' => 'Category created successfully'
                    ];
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Category creation failed', [
                        'error' => $e->getMessage(),
                        'data' => ['category_name' => $dto->category_name]
                    ]);
                    return [
                        'data' => null,
                        'message' => 'Failed to create category'
                    ];
                }
            }

            public function update( $id, CategoryDto $dto)
            {
                //check for permission role
                // check foe the user must be found and must be admin
                $user = auth()->user();
                if (!$user || auth()->user()->role !== 'admin') {
                    return [
                        'data' => null,
                        'message' => 'Unauthorized - admin only'
                    ];
                }
                DB::beginTransaction();
                try {
                    $category= Category::find($id);
                    if(!$category){
                        DB::rollBack();
                        return [
                            'data' => null,
                            'message' => 'Category not found'
                        ];
                    }  $category->update([
                        'category_name' => $dto->category_name
                    ]);

                    // commit to db
                    DB::commit();
                    //save in log file
                    Log::info('Category updated', ['id' => $id]);
                    return [
                        'data' => $category,
                        'message' => 'Category updated successfully'
                    ];

                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Category update failed', [
                        'error' => $e->getMessage(),
                        'id' => $category->id
                    ]);
                    return [
                        'data' => null,
                        'message' => 'Failed to update category'
                    ];
                }
            }

            public function delete( $id)
            {
                //check for permission role
                // check foe the user must be found and must be admin
                $user = auth()->user();
                if (!$user || auth()->user()->role !== 'admin') {
                    return [
                        'data' => null,
                        'message' => 'Unauthorized - admin only'
                    ];
                }
                DB::beginTransaction();
                try {
                    $category= Category::find($id);
                    //check if categories is found
                    if(!$category) {
                        DB::rollBack();
                        return [
                            'data' => null,
                            'message' => 'Category not found'
                        ];
                    }
                    $category->delete();
                    DB::commit();
                    //save in log file
                    Log::info('Category deleted', ['id' => $id]);
                    return [
                        'data' => null,
                        'message' => 'Category deleted successfully'
                    ];
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Category deletion failed', [
                        'error' => $e->getMessage(),
                        'id' => $id
                    ]);
                    return [
                        'data' => null,
                        'message' => 'Failed to delete category'
                    ];
                }
            }

        }
