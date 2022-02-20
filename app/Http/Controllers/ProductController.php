<?php

namespace App\Http\Controllers;

use App\Exceptions\ApplicationException;
use App\Models\Product;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use StdClass;
use Throwable;

class ProductController extends Controller
{
    public function index()
    {
        $productemp = Product::get();

        $response = new StdClass();
        $response->data = [];
        if (!empty($productemp)) {
            foreach ($productemp as $product) {
                $response->data[] = (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'quantity' => 0,
                ];
            }
        }
        return $this->sendResponse($response);
    }

    public function store(Request $request)
    {
        if (!$request->has('name') || $request->name == "") {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Name is required');
            return $this->sendResponse([]);
        }
        if (!$request->hasFile('file') || $request->file == "") {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('File is required');
            return $this->sendResponse([]);
        }
        if (!$request->has('price') || $request->price == "") {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Price is required');
            return $this->sendResponse([]);
        }
        if (!$request->has('stock') || $request->stock == "") {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Stock is required');
            return $this->sendResponse([]);
        }

        $newFormat = strtolower($request->file->getClientOriginalExtension());

        $format = explode(',', env('ACCEPTABLE_IMAGE_FORMAT'));

        //check format of file
        if (!in_array($newFormat, $format)) {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Invalid Format');
            return $this->sendResponse([]);
        }

        //check if there's space on name, space will replace to underscore
        $newName =  md5(date('Y-m-d H:i:s'). "" . $request->name);
        $fileNewName = "/images/" . $newName . "." . $newFormat;
        $fileNewName = strtolower($fileNewName);
        $fileName = strtolower($newName . "." . $newFormat);
        $transferType = env('FILE_TRANSFER_TYPE');
        $result = Storage::disk($transferType)->put($fileNewName, fopen($request->file, 'r+'));

        if (!$result) {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Error Moving File.');
            return $this->sendResponse($result);
        }

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->image = $fileName;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->save();

            $this->setMessage('Success creating Product');
            return $this->sendResponse([]);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the Administrator.');
            return $this->sendResponse($exception->getMessage());
        }
    }

    public function show(Request $request, Product $product)
    {
        $this->setMessage('Fetch Product Success.');
        return $this->sendResponse($product);
    }

    public function update(Request $request, Product $product)
    {
        if (!$request->has('name') || $request->name == "") {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Name is required');
            return $this->sendResponse([]);
        }
        if (!$request->has('price') || $request->price == "") {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Price is required');
            return $this->sendResponse([]);
        }
        if (!$request->has('stock') || $request->stock == "") {
            $this->setStatus(400);
            $this->setSuccess(false);
            $this->setMessage('Stock is required');
            return $this->sendResponse([]);
        }

        if($request->hasFile('file')) {
            $newFormat = strtolower($request->file->getClientOriginalExtension());

            $format = explode(',', env('ACCEPTABLE_IMAGE_FORMAT'));
            $path = env('FILE_URL');

            //check format of file
            if (!in_array($newFormat, $format)) {
                $this->setStatus(400);
                $this->setSuccess(false);
                $this->setMessage('Invalid Format');
                return $this->sendResponse([]);
            }

            //check if there's space on name, space will replace to underscore
            $newName =  md5(date('Y-m-d H:i:s'). "" . $request->name);
            $fileNewName = "/images/" . $newName . "." . $newFormat;
            $fileNewName = strtolower($fileNewName);
            $fileName = strtolower($newName . "." . $newFormat);
            $transferType = env('FILE_TRANSFER_TYPE');
            $result = Storage::disk($transferType)->put($fileNewName, fopen($request->file, 'r+'));

            if (!$result) {
                $this->setStatus(400);
                $this->setSuccess(false);
                $this->setMessage('Error Moving File.');
                return $this->sendResponse($result);
            }

            $product->image = $fileName;
        }

        try {
            $product->name = $request->name;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->save();

            $this->setMessage('Success creating Product');
            return $this->sendResponse([]);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the Administrator.');
            return $this->sendResponse($exception->getMessage());
        }
    }

    public function destroy(Request $request, Product $product)
    {
        try {
            $product->delete();

            $this->setMessage('Product Successfully deleted');
            return $this->sendResponse([]);
        } catch (Throwable $exception) {
            $this->setStatus(500);
            $this->setSuccess(false);
            $this->setMessage('Something went wrong. Please contact the Administrator.');
            return $this->sendResponse($exception->getMessage());
        }
    }
}
