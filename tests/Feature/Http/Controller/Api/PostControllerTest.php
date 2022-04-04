<?php

namespace Tests\Feature\Http\Controller\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Post;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store()
    {
        //$this->withoutExceptionHandLing();

        $response = $this->json('POST','/api/posts',[
            'title' => 'El post de prueba'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
            ->assertJson(['title' => 'El post de prueba'])
            ->assertStatus(201);

        $this->assertDatabasehas('posts',['title' => 'El post de prueba']);
    }

    public function test_validate_title()
    {
        $response = $this->json('POST','/api/posts',[
            'title' => ''
        ]);

        $response->assertStatus(422) //Estatus http imposible completar solicitud
            ->assertJsonValidationErrors('title');
    }
    
    public function  test_show()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('GET',"/api/posts/$post->id");

        $response->assertjsonStructure(['id', 'title','created_at','updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200);
    }

    public function  test_404_show()
    {
        
        $response = $this->json('GET','/api/posts/1000');

        $response->assertStatus(404);
    }

    public function test_update()
    {
        //$this->withoutExceptionHandLing();

        $post = factory(Post::class)->create();

        $response = $this->json('PUT',"/api/posts/$post->id",[
            'title' => 'nuevo'
        ]);

        $response->assertJsonStructure(['id','title','created_at','updated_at'])
            ->assertJson(['title' => 'nuevo'])
            ->assertStatus(200);

        $this->assertDatabasehas('posts',['title' => 'nuevo']);
    }

    public function test_delete()
    {
        //$this->withoutExceptionHandLing();

        $post = factory(Post::class)->create();

        $response = $this->json('DELETE',"/api/posts/$post->id");

        $response->assertSee(null)
            ->assertStatus(204);//sin contenido

        $this->assertDatabaseMissing('posts',['id' => $post->id]);
    }

    public function test_index()
    {
        factory(Post::class, 5)->create();

        $response = $this->json('GET','/api/posts');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at']
            ]
        ])->assertStatus(200);
    }
}
