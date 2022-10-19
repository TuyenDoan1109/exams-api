/*
1.	Exams management
    Resource API
    MVCS: Model, View, Controller, Service
    Vendor\symfony\http-foundation\Response.php
    domain: http://exams-suntech.test
- List exams(include pagination)
   * URL: /api/v1/exams
   * Method: GET
   * Request: {domain}/api/v1/exams?page=1&limit=20&column=id&sort=desc
   * Response:
    Success:
    {
        "status": true,
        "code": 200,
        "exams": [
            {
                "id": 1,
                "name": "Ayla Nolan",
                "created_at": "2022-09-19T15:42:51.000000Z",
                "updated_at": "2022-09-19T15:42:51.000000Z"
            },
            ...............
            ...............
        ],
        "meta": {
            "total": 50,
            "perPage": "2",
            "currentPage": 1
        }
    }
    
    Fail:
    {
        "status": false,
        "code": 500,
        "message": "Undefined variable: examPaginates"
    }

 - Create exams
   * URL: api/v1/exams
   * Method: POST
   * Request:
        {
            "name": "Exam name sample"
        }
   * Response:
    Success:
    {
        "status": true,
        "code": 200,
        "exam": {
            "name": "Exam 2",
            "updated_at": "2022-09-25T00:54:10.000000Z",
            "created_at": "2022-09-25T00:54:10.000000Z",
            "id": 53
        }
    }

    Fail:
    {
        "status": false,
        "code": 500,
        "message": "Message Fail"
    }

- Update exams
   * URI: api/v1/exams/{$id}
   * Method: PUT
   * Request:
   * Response:
   Success:
   {
        "status": true,
        "code": 200,
        "exam": {
            "name": "Exam 2",
            "updated_at": "2022-09-25T00:54:10.000000Z",
            "created_at": "2022-09-25T00:54:10.000000Z",
            "id": 53
        }
    }

    Fail:
    {
        "status": false,
        "code": 500,
        "message": "Message Fail"
    }
- Show an exams
   * URI: api/v1/exams/{$id}
   * Method: GET
   * Request:
   * Response:
   Success:
   {
        "status": true,
        "code": 200,
        "exam": {
            "id": 1,
            "name": "Ayla Nolan",
            "created_at": "2022-09-19T15:42:51.000000Z",
            "updated_at": "2022-09-19T15:42:51.000000Z"
        }
    }

    Fail:
    {
        "status": false,
        "code": 500,
        "message": "Message Fail"
    }