<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Link;

class LinkController extends Controller
{

    /*
    * Save Link data to the dababase 
    *
    * @param Request $request
    * @return Response
    */
    public function test(Request $request){        
        $validator = Validator::make($request->all(), [
            'link' => 'required|active_url',
        ]);

        if($validator->fails()){
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'please input a valid url',
                    'data' => []
                ]
            );
        }

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

    /*
    * Show the link/data from the database
    *
    * @param $token
    * @return Response
    */
    public function token($token){

        try{
            $token_URL = Link::where('token', $token)->firstOrFail();
            $token_URL->link;
        } catch(\Exception $e){
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'Could not find your link',
                    'data' => []
                ]
            );
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'request is successful',
                'data' => [
                    'link' => $token_URL->link,
                ]
            ]
        );
    }

}
