<?php
declare(strict_types=1);

namespace Verschuur\Laravel\RobotsTxt\Controllers;

use Illuminate\Routing\Controller;
use Verschuur\Laravel\RobotsTxt\RobotsTxtManager as Manager;

class RobotsTxtController extends Controller
{
    /**
     * By default, the production environment allows all, and every other environment allows none
     * Custom paths can be set by publishing and editing the config file
     * @return \Illuminate\Http\Response HTTP status 200 with a text/plain content type robots.txt output
     */
    public function index()
    {
        $manager = new Manager();
        $robots = implode(PHP_EOL, $manager->build());
        
        // output the entire robots.txt
        return response($robots, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
