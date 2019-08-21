<?php
declare(strict_types=1);

namespace Verschuur\Laravel\RobotsTxt;

class RobotsTxtManager
{
    public function build(): string
    {
        /**
         * Note that the config is originally loaded from this package's config file,
         * but a config file can be published to the Laravel config dir.
         * If so, due to the nature of the config setup and Larvel's config merge,
         * the original config gets completely overwritten.
         */
        $envs = config('robots-txt.paths');
        $robots = '';
        $env = config('app.env');

        // if no env is set, or one of the set envs cannot be matched against the current env, use the default
        if ($envs === null || !array_key_exists($env, $envs)) {
            $robots = $this->defaultRobot();
        } else {
            // for each user agent, get the user agent name and the paths for the agent,
            // appending them to the result string
            $agents = $envs[$env];
            foreach ($agents as $name => $paths) {
                $robot = 'User-agent: ' . $name . PHP_EOL;

                foreach ($paths as $path) {
                    $robot .= 'Disallow: ' . $path . PHP_EOL;
                }

                // append this user agent and paths to the final output
                $robots .= $robot . PHP_EOL;
            }
        }

        return $robots;
    }

    /**
     * Default 'Disallow /' for every robot
     * @return string user agent and disallow string
     */
    protected function defaultRobot()
    {
        return 'User-agent: *' . PHP_EOL . 'Disallow: /';
    }
}
