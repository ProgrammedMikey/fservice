<?php

namespace App\Http\Controllers\Api;

use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class TicketController extends ApiBaseController
{

    /**
     * @OA\Post(
     *     path="/api/v1/tickets",
     *     description="Create a ticket",
     *     operationId="tickets",
     *      @OA\Parameter(
     *         name="Authorization",
     *         description="Bearer JWT auth token",
     *         required=true,
     *         in="header",
     *     ),
     *     @OA\Parameter(
     *         description="URL of instance",
     *         in="query",
     *         name="url",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Create ticket successful"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Get information about all tickets",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */

    public function create(Request $request)
    {

            $this->validate($request, [
                'url'          => 'required|string',
                'description'  => 'required|string',
                'subject'      => 'required|string',
                'requester_id' => 'integer',
                'email'        => 'required|string',
                'phone'        => 'string',
                'priority'     => 'required|integer',
                'status'       => 'required|integer',
                'source'       => 'integer|in:2',
                'type'         => 'required|string|in:Incident',
                'cc_emails'    => 'array',
                'cc_emails.*'  => 'string',
            ]);

            $subject__str = $request->Subject;
            if ( strlen($subject__str) > 100 ) {
                $subject__str = substr($subject__str, 0, 95).'...';
            }

            $body = [
                'description' =>  $request->description,
                'subject'     =>  $request->subject,
                'requester_id' => $request->requester_id,
                'email'       =>  $request->email,
                'phone'       =>  $request->phone,
                'priority'    =>  $request->priority,
                'status'      =>  $request->status,
                'source'      =>  $request->source,
                'type'        =>  $request->type,
                'cc_emails'   =>  $request->cc_emails,
            ];

            try {

                $fsResponse = $this->guzzleClient($request)->post('api/v2/tickets', [
                    'http_errors' => true,
                    'body'  => json_encode($body),
                ]);

                $res_response = json_decode($fsResponse->getBody()->getContents());
                return response()->json($res_response, 201);
            }
            catch (\GuzzleHttp\Exception\BadResponseException $e) {
               
                $response = $e->getResponse();
                return response()->json($response->getBody()->getContents(),$response->getStatusCode());
            }
            catch (GuzzleHttp\Exception\ClientException $e) {
              
                $response = $e->getResponse();
                return response()->json($response->getBody()->getContents(),$response->getStatusCode());
            }
    }

     /**
     * @OA\Get(
     *     path="/api/v1/tickets",
     *     description="Info about all ticket",
     *     operationId="tickets",
     *      @OA\Parameter(
     *         name="Authorization",
     *         description="Bearer JWT auth token",
     *         required=true,
     *         in="header",
     *     ),
     *     @OA\Parameter(
     *         description="URL of instance",
     *         in="query",
     *         name="url",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="All ticket info"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Get information about all tickets",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */

    
    public function showAllTicket(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|string'
        ]);

        try {
            $fsResponse = $this->guzzleClient($request)->get('api/v2/tickets');
            $content = json_decode($fsResponse->getBody());
            return response()->json($content, 200);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            return response()->json($response->getBody()->getContents(),$response->getStatusCode());
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            return response()->json($response->getBody()->getContents(),$response->getStatusCode());
        }
    }
    
    /**
     * @OA\Get(
     *     path="/api/v1/tickets/{ticket_id}",
     *     description="Info about single ticket",
     *     operationId="tickets/{$ticket_id}",
     *      @OA\Parameter(
     *         description="Bearer JWT auth token",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="URL of instance",
     *         in="query",
     *         name="url",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="Ticket ID",
     *         in="path",
     *         name="ticket_id",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Single ticket info"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Get information about single ticket",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */
    public function showTicket(Request $request, $ticket_id)
        {
            $this->validate($request, [
                'url' => 'required|string'
            ]);

            try {

                $fsResponse = $this->guzzleClient($request)->get('api/v2/tickets/' . $ticket_id);
                return response()->json(json_decode($fsResponse->getBody()), 200);
            } 
            catch (\GuzzleHttp\Exception\BadResponseException $e) {
                $response = $e->getResponse();
                return response()->json($response->getBody()->getContents(),$response->getStatusCode());            }
            catch (GuzzleHttp\Exception\ClientException $e) {
                $response = $e->getResponse();
                return response()->json($response->getBody()->getContents(),$response->getStatusCode());
            }
            
        }


            /**
     * @OA\Put(
     *     path="/api/v1/tickets/{ticket_id}",
     *     description="Update single ticket",
     *     operationId="tickets/{$ticket_id}",
     *      @OA\Parameter(
     *         description="Bearer JWT auth token",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="URL of instance",
     *         in="query",
     *         name="url",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="Ticket ID",
     *         in="path",
     *         name="ticket_id",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Single ticket info"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Get information about single ticket",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */
    public function update(Request $request, $ticket_id)
    {
        $this->validate($request, [
            'url'          => 'required|string',
            'description'  => 'string',
            'subject'      => 'string',
            'requester_id' => 'integer',
            'email'        => 'string',
            'phone'        => 'string',
            'priority'     => 'integer',
            'status'       => 'integer',
            'source'       => 'integer|in:2',
            'type'         => 'string|in:Incident',
        ]);

        $subject__str = $request->Subject;
        if ( strlen($subject__str) > 100 ) {
            $subject__str = substr($subject__str, 0, 95).'...';
        }

        $body = [
            'description' =>  $request->description,
            'subject'     =>  $request->subject,
            'requester_id' => $request->requester_id,
            'email'       =>  $request->email,
            'phone'       =>  $request->phone,
            'priority'    =>  $request->priority,
            'status'      =>  $request->status,
            'source'      =>  $request->source,
            'type'        =>  $request->type,
        ];

        try {

            $fsResponse = $this->guzzleClient($request)->put('api/v2/tickets/' . $ticket_id, [
                'http_errors' => true,
                'body'  => json_encode($body),
            ]);

            $res_response = json_decode($fsResponse->getBody()->getContents());
            return response()->json($res_response, 201);
        } 
        catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            return response()->json($response->getBody()->getContents(),$response->getStatusCode());            }
        catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            return response()->json($response->getBody()->getContents(),$response->getStatusCode());
        }
        
    }

  public function showAllGroups(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|string'
        ]);

        try {
            $fsResponse = $this->guzzleClient($request)->get('api/v2/groups');
            $content = json_decode($fsResponse->getBody());
            return response()->json($content, 200);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            return response()->json($response->getBody()->getContents(),$response->getStatusCode());
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            return response()->json($response->getBody()->getContents(),$response->getStatusCode());
        }
    }
   
}