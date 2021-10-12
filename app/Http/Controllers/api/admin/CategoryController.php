<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    public function index()
    {
        //get categories
        $categories = Category::when(request()->q, function($categories) {
        $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
  


        //return with Api Resource
        return new CategoryResource(true,'List Data Categories',$categories);
        
    }

     public function store(Request $request)
     {
         $validator = Validator::make($request->all(),[
             'image'=>'required|image|mimes:jpeg,png,jpg|max:2000',
             'name'=>'required|unique:categories',
         ]);

         if($validator->fails()){
             return response()->json($validator->errors(),442);
         }

         //upload image ke server
         $image = $request->file('image');
         $image->storeAs('public/categories',$image->hashName());

         //create category
         $category = Category::create([
             'image'=>$image->hashName(),
             'name'=>$request->name,
             'slug'=>Str::slug($request->name,'-'),
         ]);


         if($category){
             //return success message with Api Resource
            return new CategoryResource(true,'Data Category Berhasi Disimpan',$category);
         }

         //return failure message with Api Resource
         return new CategoryResource(false,'Data Category Gagal Disimpan',null);
          
     }

     public function show($id){
         $category = Category::whereId($id)->first();

         if($category){
             //return success message with Api Resource
             return new CategoryResource(true,'Detail Data Category',$category);
         }

         //return failure message with Api Resource
         return new CategoryResource(false,'Detail Data Category tidak ditemukan',null);
     }

     public function update(Request $request, Category $category){

        $validator = Validator::make($request->all(),[
            'name'=>'required|unique:categories,name,'.$category->id
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),442);
        }

        //check image update
        if($request->file('image')){
            //remove old image
            Storage::disk('local')->delete('public/categories/'.basename($category->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories',$image->hashName());

            //update category with new image
            $category->update([
                'image'=> $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);

            
        }

        //update category without image
        $category->update([
            'name' => $request->name,
            'slug'=> Str::slug($request->name, '-'),
        ]);

        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Data Category Berhasil Diupdate!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Diupdate!', null);
     }


     public function destroy(Category $category){
         //remove image
         Storage::disk('local')->delete('public/categories/'.basename($category->image));

         if($category->delete()){
             //return server message with Api Resource
             return new CategoryResource(true,'Data Category Berhasi dihapus',$category);
         }

         //return failure message with Api Resource
         return new CategoryResource(false,'Data Category gagal dihapus',null);
     }
}
