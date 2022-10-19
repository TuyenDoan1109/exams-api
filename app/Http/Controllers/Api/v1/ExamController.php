<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExamService;
use Symfony\Component\HttpFoundation\Response;

class ExamController extends Controller
{
    private $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            // $exams = $this->examService->getAll();
            // return response()->json([
            //     'status' => true,
            //     'code' => Response::HTTP_OK,
            //     'exams' => $exams
            // ]);

            $limit = $request->get('limit') ?? config('app.paginate.per_page');
            $orderBys = [];
            if($request->get('column') && $request->get('sort')) {
                $orderBys['column'] = $request->get('column');
                $orderBys['sort'] = $request->get('sort');
            }
            // dd($orderBys);
            $examPaginate = $this->examService->getAll($orderBys, $limit);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'exams' => $examPaginate->items(),
                'meta' => [
                    'total' => $examPaginate->total(),
                    'perPage' => $examPaginate->perPage(),
                    'currentPage' => $examPaginate->currentPage()
                ]
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $exam = $this->examService->save(['name' => $request->name]);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'exam' => $exam
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
            $exam = $this->examService->findById($id);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'exam' => $exam
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
            $exam = $this->examService->save(['name' => $request->name], $id);
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'exam' => $exam
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
            $exam = $this->examService->delete($id);
            // Exam::find($id)->delete();
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
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
            $deletedExams = $this->examService->deleted();
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'deletedExams' => $deletedExams
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ]);
        }
    }


    //testtttttttttttttt
    public function abc() {
        return 'a-b-c';
    }

    public function xyz() {
        return 'x-y-z';
    }
}
