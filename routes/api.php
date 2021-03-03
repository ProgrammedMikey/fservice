<?php 
        //Tickets
       $router->post('/tickets', 'TicketController@create'); // create ticket
       $router->get('/tickets', 'TicketController@showAllTicket'); // get all tickets
       $router->get('/tickets/{ticket_id}', 'TicketController@showTicket'); // get specific ticket
       $router->put('/tickets/{ticket_id}', 'TicketController@update'); // update ticket

       //Ticket Notes
       $router->post('/tickets/{ticket_id}/notes', 'TicketNoteController@create');
       $router->get('/tickets/{ticket_id}/notes', 'TicketNoteController@show');
       $router->put('/tickets/{conversation_id}/notes', 'TicketNoteController@update');
       $router->post('/tickets/{ticket_id}/reply', 'TicketNoteController@reply');

       //Ticket Attachments
       $router->post('/attachments', 'TicketAttachmentController@create'); // create ticket with attachment
       $router->put('/attachments/{ticket_id}', 'TicketAttachmentController@update'); // update ticket with attachment

      $router->get('/groups', 'TicketController@showAllGroups'); // get all tickets