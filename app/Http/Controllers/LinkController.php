<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LinkController extends Controller
{

    public function test(Request $request)
    {        
        return response()->json(
            [
                'status' => 'success',
                'message' => 'request is successful',
                'data' => [
                    'link' => $request->input('link'),
                    'short_link' => 'we do not do that here...',
                ]
            ]
        );
    }

    //
}
