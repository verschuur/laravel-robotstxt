<?php
declare(strict_types=1);

namespace Verschuur\Laravel\RobotsTxt;

class RobotsTxtManager
{
    /**
     * The various paths defined in the package/app config.
     *
     * @var array
     */
    private $definedEnvironments = [];

    /**
     * The current application environment
     *
     * @var string
     */
    private $currentEnvironment = null;

    public function __construct()
    {
        /**
         * Note that the config is originally loaded from this package's config file,
         * but a config file can be published to the Laravel config dir.
         * If so, due to the nature of the config setup and Larvel's config merge,
         * the original config gets completely overwritten.
         */
        $this->definedEnvironments = config('robots-txt.paths');
        $this->currentEnvironment = config('app.env');
    }

    public function build(): string
    {
        $robots = '';

        if (!$this->hasValidPathSettings()) {
            return $this->defaultRobot();
        } else {
            // For each user agent, get the user agent name and the paths for the agent,
            // appending them to the result string.
            $agents = $this->definedEnvironments[$this->currentEnvironment];
            foreach ($agents as $name => $paths) {
                $robot = 'User-agent: ' . $name . PHP_EOL;

                foreach ($paths as $path) {
                    $robot .= 'Disallow: ' . $path . PHP_EOL;
                }

                // Append this user agent and paths to the final output.
                $robots .= $robot . PHP_EOL;
            }
        }

        return $robots;
    }

    /**
     * Returns 'Disallow /' as the default for every robot
     *
     * @return string user agent and disallow string
     */
    protected function defaultRobot(): string
    {
        return 'User-agent: *' . PHP_EOL . 'Disallow: /';
    }

    /**
     * Validates the set environment paths and the current environment
     *
     * If:
     * - The defined environments are null (missing from config)
     * - One or more enviroments are set, but have no pathss
     * - The current environment cannot be matched against the defined environments
     * then return false;
     *
     * @return boolean
     */
    protected function hasValidPathSettings(): bool
    {
        // The'robots-txt.paths' config path return null.
        if ($this->definedEnvironments === null) {
            return false;
        }

        // The'robots-txt.paths' config path return an empty array.
        if (empty($this->definedEnvironments)) {
            return false;
        }

        // The current environment cannot be matched against the defined environments.
        if (!array_key_exists($this->currentEnvironment, $this->definedEnvironments)) {
            return false;
        }
        
        return true;
    }
}
