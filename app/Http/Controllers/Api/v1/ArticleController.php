<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Article as ArticleResource;
use App\Services\ArticleService;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    private $articleService;

    public function __construct(ArticleService $articleService) {
        $this->articleService = $articleService; 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $limit = $request->get('limit') ?? config('app.paginate.per_page');
            $orderBys = [];
            if($request->get('column') && $request->get('sort')) {
                $orderBys['column'] = $request->get('column');
                $orderBys['sort'] = $request->get('sort');
            }
            $articles = $this->articleService->getAll($orderBys, $limit);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'articles' => ArticleResource::collection($articles),
                'meta' => [
                    'total' => $articles->total(),
                    'perPage' => $articles->perPage(),
                    'currentPage' => $articles->currentPage()
                ]
            ]);
        } catch(\Exception $e){
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        try{
            $article = $this->articleService->save([
                'title' => $request->title,
                'body' => $request->body
            ]);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Thêm sản phẩm thành công',
                'article' => new ArticleResource($article)
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $article = $this->articleService->findById($id);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'article' => new ArticleResource($article)
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {  
        try{
            $article = $this->articleService->save([
                'title' => $request->title,
                'body' => $request->body
            ], $id);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Update sản phẩm thành công',
                'article' => new ArticleResource($article)
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $article = $this->articleService->delete($id);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Xoá sản phẩm thành công'
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleted() {
        try{
            $deletedArticle = $this->articleService->deleted();
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'deletedArticle' => $deletedArticle
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }
}
