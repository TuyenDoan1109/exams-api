<?php

use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory(Article::class, 30)->create();
        DB::table('articles')->insert([
            [
                'title' => 'title 1',
                'body' => 'body 1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'title 2',
                'body' => 'body 2',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
