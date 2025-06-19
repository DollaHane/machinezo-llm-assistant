<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function create(Request $request) {
        /* 
        ## TO DO:
        - Rate limit
        - Auth check for specific email address
        - Parse CSV Data
        - Run LLM Queries
        - Insert Data
        **/

        Log::info($request->all());
        return response()->json(['message', 'This is from the server'], 200);
    }
}
