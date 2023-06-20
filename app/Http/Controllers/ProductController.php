<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use PhpOption\None;

class ProductController extends Controller
{
 
    public function index(): View
    {
        $products = Product::latest()->paginate(5);
      
        return view('products.index',compact('products'))
                    ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
        //dd($request->all());

        $image = $request->image;
        //return($image->extension());
        $imageName = time().'.'.$image->extension();
        $request->image->move(public_path('images'), $imageName);

        $product = new Product();
        $product->name = $request->name;
        $product->detail = $request->detail;
        $product->image = $imageName;
        $product->save();
        //Product::create($request->all());
       
        return redirect()->route('products.index')
                        ->with('success','Product created successfully.');
    }

    public function show(Product $product): View
    {
        return view('products.show',compact('product'));
    }

   
    public function edit(Product $product)
    {
        return view('products.edit',compact('product'));
    }


    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
        $img_exist = $request->has('image');
        if($img_exist){
            $image = $request->image;
            $imageName = time().'.'.$image->extension();
            $request->image->move(public_path('images'), $imageName);
        }
        
        //return($image->extension());
        
        $product->name = $request->name;
        $product->detail = $request->detail;
        if($img_exist){
            $product->image = $imageName;
        }
        $product->save();

        //$product->update($request->all());
      
        return redirect()->route('products.index')->withInput()
                        ->with('success','Product updated successfully');
    }

   
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
       
        return redirect()->route('products.index')
                        ->with('success','Product deleted successfully');
    }
}
