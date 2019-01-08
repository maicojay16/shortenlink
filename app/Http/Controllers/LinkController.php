<?php

namespace App\Http\Controllers;

use Validator;
use App\Link;
use App\LinkAnalytic;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /*
    * Save Link data to the dababase 
    *
    * @param Request $request
    * @return Response
    */
    public function link(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'link' => 'required|active_url', 
        ]);

        if ($validator->fails()) {
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

        try {
            $link->save();
        } catch (\Exception $e) {
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
    * @param Request $request, $token
    * @return Response
    */
    public function token(Request $request, $token)
    {
        $validator = Validator::make(
            [
                'token' => $token
            ], 
            [
                'token' => 'exists:links,token|string', 
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => 'please input a valid token', 
                    'data' => []
                ]
            );
        }

        try {
            $token_URL = Link::where('token', $token)->firstOrFail();
            $token_URL->link;
        } catch(\Exception $e) {
            return response()->json(
                [
                    'status' => 'failed', 
                    'message' => 'Could not find your link', 
                    'data' => []
                ]
            );
        }

        $link_analytic = new LinkAnalytic;
        $link_analytic->link_id = $token_URL->id;
        $link_analytic->user_ip = $request->ip();
        $link_analytic->user_agent = $request->header('USER-Agent');
        $link_analytic->referral_link = $request->server('HTTP_REFERER');

        try {
            $link_analytic->save();
        } catch(\Exception $e) {
            //
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
