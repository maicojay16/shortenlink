<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Link;

class LinkController extends Controller
{

    /*
    * @param int $number
    * @return mixed
    */
    private function Jay(int $number): int
    {
        return $number;
    }


    /*
    * @param Request $request
    * @return response()->json
    */
    public function test(Request $request)
    {        
        // return $this->Jay(100);

        $link = new Link;
        $token = str_random(12);
        $link->link = $request->input('link');
        $link->token = $token;

        try{
            $link->save();
        } catch(\Exception $e){
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'could not create short link',
                    'data' => []
                ]
            );
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'request is successful',
                'data' => [
                    'link' => $request->input('link'),
                    'short_link' => $token,
                ]
            ]
        );
    }

}
