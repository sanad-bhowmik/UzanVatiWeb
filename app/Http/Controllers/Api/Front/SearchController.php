<?php

namespace App\Http\Controllers\Api\Front;

use App\{
  Models\Product,
  Models\Category,
  Models\Attribute,
  Models\Subcategory,
  Models\Childcategory,
  Models\AttributeOption
};

use App\{
  Http\Controllers\Controller,
  Http\Resources\CategoryResource,
  Http\Resources\AttributeResource,
  Http\Resources\ProductlistResource,
  Http\Resources\SubcategoryResource,
  Http\Resources\ChildcategoryResource,
  Http\Resources\AttributeOptionResource
};

use Illuminate\{
  Http\Request,
  Support\Collection
};

use Validator;

class SearchController extends Controller
{

  public function categories1(Request $request, $categoryId = null)
  {
    try {
      $categories = ($categoryId) ? Category::findOrFail($categoryId) : Category::all();

      if ($categories instanceof Collection) {
        $transformedCategories = $categories->map(function ($category) {
          return $this->transformCategory($category);
        });
      } else {
        $transformedCategories = $this->transformCategory($categories);
      }

      return response()->json([
        'status' => true,
        'data' => $transformedCategories,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => false,
        'data' => [],
        'error' => ['message' => $e->getMessage()]
      ]);
    }
  }

  protected function transformCategory($category)
  {
    $transformedCategory = [
      'id' => $category->id,
      'name' => $category->name,
      'icon' => url('/') . '/assets/images/categories/' . $category->icon,
    ];

    if ($category->products) {
      $transformedCategory['products'] = $category->products->map(function ($product) {
        $user_name = $product->user->name;
        $user_email = $product->user->email;
        $user_address = $product->user->shop_address;
        $shop_image = $product->user->shop_image;
        return [
          'id' => $product->id,
          'title' => $product->name,
          'user_id' => $product->user_id,
          'owner_name' => $user_name,
          'email' => $user_email,
          'shop_address' => $user_address,
          'shop_image' => url('/') . '/assets/images/vendorbanner/' . $shop_image,
          'thumbnail' => url('/') . '/assets/images/thumbnails/' . $product->thumbnail,
          'rating' => ($product->rating !== null) ? $product->rating : 0,
          'type' => $product->type,
          'product_type' => $product->product_type,
          'details' => $product->details,
          'stock' => $product->stock,
          'views' => $product->views,
          'current_price' => $product->price,
          'previous_price' => $product->previous_price,
        ];
      });
    }

    return $transformedCategory;
  }

  public function category($id)
  {
    try {
      $cat = Category::where('status', '=', 1)->where('id', $id)->get();
      return response()->json(['status' => true, 'data' => CategoryResource::collection($cat), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function subcategories($id)
  {
    try {
      $subcats = Subcategory::where('category_id', $id)->where('status', '=', 1)->get();
      return response()->json(['status' => true, 'data' => SubcategoryResource::collection($subcats), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function childcategories($id)
  {
    try {
      $childcats = Childcategory::where('subcategory_id', $id)->where('status', '=', 1)->get();
      return response()->json(['status' => true, 'data' => ChildcategoryResource::collection($childcats), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function attributes(Request $request, $id)
  {
    try {


      $rules = [
        'type' => 'required'
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['status' => false, 'data' => [], 'error' => $validator->errors()]);
      }

      $type          = $request->type;
      $checkType =  in_array($type, ['category', 'subcategory', 'childcategory']);
      if (!$checkType) {
        return response()->json(['status' => false, 'data' => [], 'error' => ["message" => "This type doesn't exists."]]);
      }


      if ($type == 'category') {
        $attributes = Attribute::where('attributable_id', $id)->where('attributable_type', 'App\Models\Category')->get();
      }
      if ($type == 'subcategory') {
        $attributes = Attribute::where('attributable_id', $id)->where('attributable_type', 'App\Models\Subcategory')->get();
      }
      if ($type == 'childcategory') {
        $attributes = Attribute::where('attributable_id', $id)->where('attributable_type', 'App\Models\Childcategory')->get();
      }

      return response()->json(['status' => true, 'data' => AttributeResource::collection($attributes), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function attributeoptions($id)
  {
    try {
      $attributeOpts = AttributeOption::where('attribute_id', $id)->get();
      return response()->json(['status' => true, 'data' => AttributeOptionResource::collection($attributeOpts), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => true, 'data' => [], 'error' => ['message' => $e->getMessage()]]);
    }
  }

  public function search(Request $request)
  {
    try {
      $sort = $request->sort;
      $search = $request->term;

      if (!empty($request->category)) {
        $cat = Category::find($request->category);
      } else {
        $cat = NULL;
      }
      if (!empty($request->subcategory)) {
        $subcat = Subcategory::find($request->subcategory);
      } else {
        $subcat = NULL;
      }
      if (!empty($request->childcategory)) {
        $childcat = Childcategory::find($request->childcategory);
      } else {
        $childcat = NULL;
      }

      $prods = Product::when($cat, function ($query, $cat) {
        return $query->where('category_id', $cat->id);
      })
        ->when($subcat, function ($query, $subcat) {
          return $query->where('subcategory_id', $subcat->id);
        })
        ->when($childcat, function ($query, $childcat) {
          return $query->where('childcategory_id', $childcat->id);
        })
        ->when($search, function ($query, $search) {
          return $query->where('name', 'like', '%' . $search . '%');
        })
        ->when($sort, function ($query, $sort) {
          if ($sort == 'date_desc') {
            return $query->orderBy('id', 'DESC');
          } elseif ($sort == 'date_asc') {
            return $query->orderBy('id', 'ASC');
          } elseif ($sort == 'price_desc') {
            return $query->orderBy('price', 'DESC');
          } elseif ($sort == 'price_asc') {
            return $query->orderBy('price', 'ASC');
          }
        })
        ->when(empty($sort), function ($query, $sort) {
          return $query->orderBy('id', 'DESC');
        })
        ->where('status', 1)
        ->get();

      $prods = (new Collection(Product::filterProducts($prods)));

      return response()->json(['status' => true, 'data' => ProductlistResource::collection($prods->flatten(1)), 'error' => []]);
    } catch (\Exception $e) {
      return response()->json(['status' => false, 'data' => [], 'error' => []]);
    }
  }
}
