<?php

return [
    '/' => ['LoginController', 'home'], 
    '/login' => ['LoginController', 'login'],
    '/cadastrar' => ['LoginController', 'registerView'],
    '/register' => ['LoginController', 'register'],
    '/clientes' => ['UsersController', 'list'],
    '/save' => ['UsersController', 'save']
];
