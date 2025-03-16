<?php

use App\Mail\Rabbitmq\TodoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail', function () {
    // return view('mail.default');
    // Mail::to('email@test.com')->send(new TodoMail());
    return 'ok';
});
