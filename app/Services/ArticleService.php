<?php

namespace App\Services;

use App\Models\Article;

class ArticleService 
{
    public function getall($orderBys = [], $limit = 10) {
        $query = Article::query();

        if($orderBys) {
            // dd($orderBys);
            $query->orderBy($orderBys['column'], $orderBys['sort']);
        }
        return $query->paginate($limit);
    }

    public function save(array $data, int $id = null) {
        return Article::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'title' => $data['title'],
                'body' => $data['body']
            ]
        );
    }

    public function findById($id) {
        return Article::find($id);
    }

    public function delete($ids = []) {
        return Article::destroy($ids);
    }

    public function deleted() {
        return Article::onlyTrashed()->get();
    }
}

