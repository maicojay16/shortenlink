<?php

namespace App\Http\Controllers;

use Validator;
use Log;
use App\Link;
use App\LinkAnalytic;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

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
        Log::info('Checking if URL is valid: '.$request->input('link'));
        
        $validator = Validator::make($request->all(), [
            'link' => 'required|active_url', 
        ]);

        if ($validator->fails()) {
            Log::alert('Please input a valid url: '.$request->input('link'));
            return ResponseHelper::createJsonResponse('failed', 'please input a valid url', []);
        }

        $link = new Link;
        $token = str_random(12);
        $link->link = $request->input('link');
        $link->token = $token;

        try {
            $link->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::createJsonResponse('failed', 'could not create short link', []); 
        }
        
        return ResponseHelper::createJsonResponse('success', 'request is successful', 
                                                [
                                                    'link' => $request->input('link'),'short_link' => $token,
                                                ]);

    }
    
    /*
    * Show the link/data from the database
    *
    * @param Request $request
    * @param string $token
    * @return Response
    */
    public function token(Request $request, $token)
    {
        Log::info('Showing data from token: '.$token);

        $validator = Validator::make(
            [
                'token' => $token
            ], 
            [
                'token' => 'exists:links,token|string', 
            ]
        );

        if ($validator->fails()) {
            Log::alert('Please input a valid token: '.$token);
            return ResponseHelper::createJsonResponse('failed', 'please input a valid token', []);
        }

        try {
            $token_URL = Link::where('token', $token)->firstOrFail();
            $token_URL->link;
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::createJsonResponse('failed', 'Could not find your token', []);
        }

        $linkAnalytic = new LinkAnalytic;
        $linkAnalytic->link_id = $token_URL->id;
        $linkAnalytic->user_ip = $request->ip();
        $linkAnalytic->user_agent = $request->header('USER-Agent');
        $linkAnalytic->referral_link = $request->server('HTTP_REFERER');

        try {
            $linkAnalytic->save();
        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }
        
        return ResponseHelper::createJsonResponse('success', 'request is successful', ['link' => $token_URL->link]);
    }

    /*
    * Show the number of clicks from token data 
    *
    * @param Request $request
    * @param string $token
    * @return ResponseHelper
    */
    public function analytic(Request $request, $token)
    {
        Log::info('Showing data from token: '.$token);

        $validator = Validator::make(
            [
                'token' => $token
            ], 
            [
                'token' => 'exists:links,token|string', 
            ]
        );

        if ($validator->fails()) {
            Log::info('Please input a valid token: '.$token);
            return ResponseHelper::createJsonResponse('failed', 'please input a valid token', []);
        }

        try {
            $tokenURL = Link::where('token', $token)->firstOrFail();
            $numberOfClicks = array();
            foreach ($tokenURL->linkAnalytic()->get() as $linkAnalytic) {
                $numberOfClicks[] = $linkAnalytic;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::createJsonResponse('failed', 'Could not find your token', []);
        }

        return ResponseHelper::createJsonResponse('success', 'request is successful', 
                                                [
                                                    'number of clicks' => count($numberOfClicks),

                                                ]);

        // $l = LinkAnalytic::where('id', 4)->firstOrFail();
        // echo $l->link()->get()[0]->token;
    }
}
