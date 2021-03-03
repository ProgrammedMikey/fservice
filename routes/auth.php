<?php
 // Matches "/api/v1/register
$router->post('register', 'AuthController@register');

 // Matches "/api/v1/login
$router->POST('login', 'AuthController@login');


 // Matches "/api/v1/profile
 $router->get('profile', 'UserController@profile');

 // Matches "/api/v1/users/1 
 //get one user by id from lumen/sqlite server
 $router->get('users/{id}', 'UserController@singleUser');

 // Matches "/api/v1/users from lumen/sqlite server
 $router->get('users', 'UserController@allUsers');