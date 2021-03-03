<?php

namespace App\Http\Controllers\Api;

use Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use function GuzzleHttp\Psr7\stream_for;

class TicketAttachmentController extends ApiBaseController
{

       /**
     * @OA\Get(
     *     path="/api/v1/attachments",
     *     description="Create a new ticket with attachment",
     *     operationId="attachments",
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
     *     @OA\Response(
     *         response="200",
     *         description="Created ticket with attachment successful"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="JWT auth token is not valid/is expired"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     summary="Create ticket with attachment",
     *     tags={
     *         "ticket"
     *     }
     * )
     * */

    public function create(Request $request)
    {
        $this->validate($request, [
            'url'          => 'required|string',
            'description'  => 'string',
            'subject'      => 'string',
            'email'        => 'string',
            'priority'     => 'integer',
            'status'       => 'integer',
            'file'         => 'required|file',
            'file_name'    => 'required|string|max:255',
            'content_type' => 'required|string'
        ]);

        $subject__str = $request->Subject;
        if ( strlen($subject__str) > 100 ) {
            $subject__str = substr($subject__str, 0, 95).'...';
        }

        $filename   = str_replace(" ", "_", $request->file->getClientOriginalName());
        $file       = $request->file->move(storage_path('tmp/'), $filename);
        $fsResponse = $this->guzzleClient($request);
      
        $body = [
            'description' =>  $request->description,
            'subject'     =>  $request->subject,
            'email'       =>  $request->email,
            'priority'    =>  2,
            'status'      =>  2,
            'attachments'=> fopen($file, 'r')
        ];

        try {

            $r = $fsResponse->post('api/v2/tickets', [
                'http_errors' => true,
                'body'  => json_encode($body),
            ]);

            $res_response = json_decode($fsResponse->getBody()->getContents());

        }
            catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            $content = $response->getBody()->getContents();
            Log::error('>> TicketAttachment::search() >> \GuzzleHttp\Exception\BadResponseException');
            Log::error('>> Code: [' . $response->getStatusCode() . ']');
            Log::error('>> Params: [' . print_r($request->all(), true) . ']');
            Log::error('>> Error: [' . print_r($content, true) . ']');

            return response()->json($content, $response->getStatusCode());
        } 
            catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $content = $response->getBody()->getContents();
            Log::error('>> TicketAttachment::search() >> \GuzzleHttp\Exception\ClientException');
            Log::error('>> Code: [' . $response->getStatusCode() . ']');
            Log::error('>> Params: [' . print_r($request->all(), true) . ']');
            Log::error('>> Error: [' . print_r($content, true) . ']');

            return response()->json($content, $response->getStatusCode());
        }

    }

    /**
     * @OA\Put(
     *     path="/api/v1/attachments/{ticket_id}",
     *     description="Put attachments to single ticket",
     *     operationId="attachments/{$ticket_id}",
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
     *         description="Updated ticket with attachment successful"
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
            'email'        => 'string',
            'priority'     => 'integer',
            'status'       => 'integer',
            'file'         => 'required|file',
            'file_name'    => 'required|string|max:255',
            'content_type' => 'required|string'
        ]);

        $subject__str = $request->Subject;
        if ( strlen($subject__str) > 100 ) {
            $subject__str = substr($subject__str, 0, 95).'...';
        }

        $filename = str_replace(" ", "_", $request->file->getClientOriginalName());
        $file = $request->file->move(storage_path('tmp/'), $filename);
        $fsResponse = $this->guzzleClient($request);

        $body = fopen($file, 'r');
        try {
        $r = $fsResponse->put('api/v2/tickets' . $ticket_id, [
            'multipart' => [
                    [
                        'name'         => 'attachments',
                        'FileContents' => fopen($file, 'r'),
                        'contents'     => fopen($file, 'r')
                    ],
                ]
        ]);
        return response()->json(json_decode($r->getBody()->getContents()),$r->getStatusCode());

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $response = $e->getResponse();
            $content = $response->getBody()->getContents();
            Log::error('>> TicketAttachment::search() >> \GuzzleHttp\Exception\BadResponseException');
            Log::error('>> Code: [' . $response->getStatusCode() . ']');
            Log::error('>> Params: [' . print_r($request->all(), true) . ']');
            Log::error('>> Error: [' . print_r($content, true) . ']');

            return response()->json($content, $response->getStatusCode());
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $content = $response->getBody()->getContents();
            Log::error('>> TicketAttachment::search() >> \GuzzleHttp\Exception\ClientException');
            Log::error('>> Code: [' . $response->getStatusCode() . ']');
            Log::error('>> Params: [' . print_r($request->all(), true) . ']');
            Log::error('>> Error: [' . print_r($content, true) . ']');

            return response()->json($content, $response->getStatusCode());
        }
    }

}