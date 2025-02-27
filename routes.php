<?php

return [
    '/' => ['LoginController', 'home'],
    '/login' => ['LoginController', 'login'],
    '/cadastrar' => ['LoginController', 'registerView'],
    '/register' => ['LoginController', 'register'],
    '/clientes' => ['ClientsController', 'list'],
    '/save' => ['ClientsController', 'save'],
    '/create' => ['ClientsController', 'create'],
    '/edit' => ['ClientsController', 'edit'],
    '/deleteClient' => ['ClientsController', 'delete'],
    '/allClients' => ['ClientsController', 'allClients']
];

