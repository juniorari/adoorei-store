<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use ErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    const PER_PAGE = 20;
    const FAILED = 'Falha no serviço. Tente novamente.';

    public function index(Request $request)
    {
        $products = ProductResource::collection($this->getProductsBySearch($request, self::PER_PAGE))
            ->response()
            ->getData(true);

        return $this->response(Response::HTTP_OK, 'Success', $products);
    }

    public function store(Request $request)
    {
        $pr = (new ProductRequest());

        $validator = Validator::make($request->all(),
            $pr->rules(), $pr->messages()
        );

        if ($validator->fails()) {
            return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, 'Erro de validação', ['data' => $validator->errors()->toArray()]);
        }

        if ($product = Product::create($request->all())) {
            $product = (new ProductResource($product))
                ->response()
                ->getData(true);
            return $this->response(Response::HTTP_CREATED, 'Produto foi criado com sucesso', $product);
        }

        throw new ErrorException(self::FAILED, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->response(Response::HTTP_NOT_FOUND, 'Produto não encontrado', []);
        }
        $product = (new ProductResource($product))
            ->response()
            ->getData(true);

        return $this->response(Response::HTTP_OK, 'Success', $product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->response(Response::HTTP_NOT_FOUND, 'Produto não encontrado', []);
        }


        if ($product->update($request->all())) {
            $product = (new ProductResource($product))
                ->response()
                ->getData(true);
            return $this->response(Response::HTTP_OK, 'Produto foi atualizado com sucesso', $product);
        }

        throw new ErrorException(self::FAILED, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->response(Response::HTTP_NOT_FOUND, 'Produto não encontrado', []);
        }

        if ($product->delete()) return $this->response(Response::HTTP_OK, 'Produto apagado com sucesso');

        throw new ErrorException(self::FAILED, Response::HTTP_INTERNAL_SERVER_ERROR);

    }


    /**
     * wrap a result into json response.
     *
     * @param int $code
     * @param string $message
     * @param array $resource
     * @return JsonResponse
     */
    private function response(int $code, string $message, ?array $resource = []): JsonResponse
    {
        return responseJson($code, $message, $resource);
    }

    /**
     * query products by search
     *
     * @param Request $request
     * @param int $number
     * @return LengthAwarePaginator
     */
    public function getProductsBySearch(Request $request, int $number): LengthAwarePaginator
    {
        $products = Product::where('name', 'LIKE', "%{$request->name}%")
            ->latest()
            ->paginate($number)
            ->appends($request->query());

        if (isset($request->name)) {
            if (count($products) > 0) return $products;

            throw new ModelNotFoundException("Produto não encontrado");
        };

        return $products;
    }


}
