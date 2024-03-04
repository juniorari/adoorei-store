<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesProduct;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    const FAILED = 'Falha no serviço. Tente novamente.';

    public function index()
    {
        $sales = Sale::with('products');
        $response = [
            'data' => $sales->get(),

        ];
        return $this->response(Response::HTTP_OK, 'Lista de vendas', $response);
    }

    public function create(Request $request)
    {
        DB::beginTransaction();
        try {

            if (!$request->products || !is_array($request->products)) {
                return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, 'Parâmetro obrigatório não enviado', []);
            }

            if (!count($request->products)) {
                return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, 'É necessário informar ao menos um produto', []);
            }

            $prods = [];
            $total = 0;
            foreach ($request->products as $product) {
                $prod = Product::find($product['id']);
                if (!$prod) {
                    return $this->response(Response::HTTP_NOT_FOUND, "Produto com o ID '{$product['id']}' não existe", []);
                }
                if (!isset($product['amount'])) {
                    return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, "É necessário informar a quantidade para o produto com ID '{$product['id']}' ", []);
                }
                $pr['product_id'] = $prod->id;
                $pr['name'] = $prod->name;
                $pr['price'] = $prod->price;
                $pr['amount'] = $product['amount'];
                $prods[] = $pr;
                $total += $product['amount'] * $pr['price'];
            }
            $sale = Sale::create([
                'amount' => $total,
            ]);
            foreach ($prods as $prod) {
                SalesProduct::create([
                    'product_id' => $prod['product_id'],
                    'sale_id' => $sale->id,
                    'name' => $prod['name'],
                    'amount' => $prod['amount'],
                    'price' => $prod['price'],
                ]);
            }
            $response = [
                'data' => [
                    'sales_id' => $sale->id,
                    'amount' => $total,
                    'products' => $prods,
                ]
            ];
            DB::commit();
            return $this->response(Response::HTTP_OK, 'Venda cadastrada com sucesso', $response);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), []);
        }
    }

    public function show($id)
    {
        $sale = Sale::find($id);

        if (!$sale) {
            return $this->response(Response::HTTP_NOT_FOUND, 'Venda não encontrada', []);
        }
        $sale = $sale->with('products');

        return $this->response(Response::HTTP_OK, 'Success', ['data' => $sale->first()]);

    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::find($id);

            if (!$sale) {
                return $this->response(Response::HTTP_NOT_FOUND, 'Venda não encontrada', []);
            }

            $sale = $sale->with('products')->first();
            foreach ($sale->products as $product) {
                $product->delete();
            }
            if ($sale->delete()) {
                DB::commit();
                return $this->response(Response::HTTP_OK, 'Venda apagada com sucesso');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->response(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage(), []);
        }
        throw new ErrorException(self::FAILED, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function update(Request $request, $id)
    {
        try {

            DB::beginTransaction();
            $sale = Sale::find($id);

            if (!$sale) {
                return $this->response(Response::HTTP_NOT_FOUND, 'Venda não encontrada', []);
            }

            if (!$request->products || !is_array($request->products)) {
                return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, 'Parâmetro obrigatório não enviado', []);
            }

            if (!count($request->products)) {
                return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, 'É necessário informar ao menos um produto', []);
            }

            $sale = $sale->with('products')->first();

            $prods = [];
            $total = 0;
            foreach ($request->products as $product) {
                $prod = Product::find($product['id']);
                if (!$prod) {
                    return $this->response(Response::HTTP_NOT_FOUND, "Produto com o ID '{$product['id']}' não existe", []);
                }
                if (!isset($product['amount'])) {
                    return $this->response(Response::HTTP_UNPROCESSABLE_ENTITY, "É necessário informar a quantidade para o produto com ID '{$product['id']}' ", []);
                }
                $pr['product_id'] = $prod->id;
                $pr['name'] = $prod->name;
                $pr['price'] = $prod->price;
                $pr['amount'] = $product['amount'];
                $prods[] = $pr;
                $total += $product['amount'] * $pr['price'];
            }
            $sale->update([
                'amount' => $total,
            ]);
            foreach ($prods as $prod) {

                if ($sp = SalesProduct::where('product_id', $prod['product_id'])->where('sale_id', $sale->id)->first()) {
                    $update = [
                        'name' => $prod['name'],
                        'price' => $prod['price'],
                        'amount' => $prod['amount'],
                    ];
                    $sp->update($update);
                    $total += $prod['amount'] * $prod['price'];
                } else {
                    SalesProduct::create([
                        'product_id' => $prod['product_id'],
                        'sale_id' => $sale->id,
                        'name' => $prod['name'],
                        'amount' => $prod['amount'],
                        'price' => $prod['price'],
                    ]);
                }
            }
            $response = [
                'data' => [
                    'sales_id' => $sale->id,
                    'amount' => $total,
                    'products' => $prods,
                ]
            ];
            DB::commit();

            return $this->response(Response::HTTP_OK, 'Venda alterada com sucesso', $response);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), []);
        }
    }

    private function response(int $code, string $message, ?array $resource = []): JsonResponse
    {
        return responseJson($code, $message, $resource);
    }
}
