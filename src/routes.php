<?php
declare(strict_types=1);

Route::get('/robots.txt', 'Verschuur\Laravel\RobotsTxt\Controllers\RobotsTxtController@index')->name('robots.txt');
