<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->load(['brand', 'user']);

        return [
            'id' => $this->id,
            'shop_id' => $this->user_id,
            'shop_name' => $this->user ? $this->user->shop_name : null,
            'title' => $this->name,
            'thumbnail' => url('/') . '/assets/images/thumbnails/' . $this->thumbnail,
            'rating' => $this->ratings()->avg('rating') > 0 ? round($this->ratings()->avg('rating'), 2) : round(0.00, 2),
            'current_price' => $this->mainPrice($this->price),
            'previous_price' => $this->mainPrice($this->previous_price),
            'sale_end_date' => $this->when($this->is_discount == 1, $this->discount_date),
            'discount_percent' => $this->discount_percent,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'type' => $this->type,
            'product_type' => $this->product_type,
            'details' => strip_tags($this->details),
            'category_id' => $this->category_id,
            'category_name' => $this->category->name,
            'stock' => $this->stock,
            'views' => $this->views,
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand ? $this->brand->brand_name : null,
        ];
    }

    private function mainPrice($price)
    {
        return number_format($price, 2);
    }
}
