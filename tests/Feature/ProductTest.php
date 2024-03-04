<?php

namespace Tests\Feature;

 use App\Models\Product;
 use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_listando_os_produtos(): void
    {
        $response = $this->get(route('products'));
        $response->assertStatus(200);
    }

    public function test_criando_um_produto(): void
    {

        $data = [
            'name' => 'Celular Teste',
            'price' => 1000,
            'description' => 'Lorem ipsum dolor sit ammet, consectet',
        ];
        $response = $this->post(route('product.create'), $data);
        $response->assertStatus(201);
    }

    public function test_validar_criacao_de_um_produto(): void
    {

        $prod = Product::create([
            'name' => 'Celular Teste',
            'price' => 1000,
            'description' => 'Lorem ipsum dolor sit ammet, consectet',
        ]);

        $data = [
            'name' => 'Celular Teste',
            'price' => 1000,
            'description' => 'Lorem ipsum dolor sit ammet, consectet',
        ];
        $response = $this->post(route('product.create'), $data);
        $response->assertStatus(422);
    }

    public function test_editando_um_produto(): void
    {

        $prod = Product::create([
            'name' => 'Celular Teste',
            'price' => 1000,
            'description' => 'Lorem ipsum dolor sit ammet, consectet',
        ]);

        $data = [
            'name' => 'Celular Teste Alterado',
            'price' => 10,
            'description' => 'Lorem ipsum dolor',
        ];
        $response = $this->put(route('product.update', $prod->id), $data);
        $response->assertStatus(200);

    }

    public function test_buscando_um_produto (): void
    {
        $prod = Product::create([
            'name' => 'Celular Teste',
            'price' => 1000,
            'description' => 'Lorem ipsum dolor sit ammet, consectet',
        ]);

        $response = $this->get(route('product.show', $prod->id));
        $response->assertStatus(200);

        $response = $this->get(route('product.show', 1000));
        $response->assertStatus(404);
    }
}
