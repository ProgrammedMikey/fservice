<?php

namespace App\Http\Controllers\Api;

use Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketNoteController extends ApiBaseController
{
  /**
     * @OA\Post(
     *     path="/api/v1/tickets/{ticket_id}/notes",
     *     description="Create a Note",
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

    public function create(Request $request, $ticket_id)
    {

            $this->validate($request, [
                'url'          => 'required|string',
                'body'         => 'required|string',
                'private'      => 'bool',
            ]);

            $subject__str = $request->Subject;
            if ( strlen($subject__str) > 100 ) {
                $subject__str = substr($subject__str, 0, 95).'...';
            }

            $body = [
                'body'        =>  $request->body,
                'private'     =>  $request->private,
            ];

            try {

                $fsResponse = $this->guzzleClient($request)->post('api/v2/tickets/' . $ticket_id . '/notes', [
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
     *     path="/api/v1/tickets/{ticket_id}/notes",
     *     description="Info of all notes and conversations from a ticket",
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
     *         description="All ticket notes info"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Get information about all notes and conversations of a tickets",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */

    
    public function show(Request $request, $ticket_id)
    {
        $this->validate($request, [
            'url' => 'required|string'
        ]);

        try {
            $fsResponse = $this->guzzleClient($request)->get('api/v2/tickets/' . $ticket_id . '/conversations', );
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
     * @OA\Put(
     *     path="/api/v1/tickets/{ticket_id}/notes",
     *     description="Update notes on single ticket",
     *     operationId="tickets/{ticket_id}/notes",
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
     *         description="Update a tickets notes"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Update note on a ticket",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */
   
    public function update(Request $request, $conversation_id)
    {
        $this->validate($request, [
            'url'   => 'required|string',
            'body'  => 'string',
        ]);

        $body = [
            'body' =>  $request->body,
        ];

        try {

            $fsResponse = $this->guzzleClient($request)->put('api/v2/conversations/' . $conversation_id, [
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

        
    /**
     * @OA\Post(
     *     path="/api/v1/tickets/{ticket_id}/reply",
     *     description="Reply to a ticket id",
     *     operationId="tickets/{ticket_id}/reply",
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
     *         description="Reply to ticket note"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Reply to ticket by proving the ticket_id and body",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */
   
    public function reply(Request $request, $ticket_id)
    {
        $this->validate($request, [
            'url'   => 'required|string',
            'body'  => 'required|string',
            'from_email' => 'string'
        ]);

        $body = [
            'body' =>  $request->body,
        ];

        try {

            $fsResponse = $this->guzzleClient($request)->post('api/v2/tickets/' . $ticket_id . '/reply', [
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
}