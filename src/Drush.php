<?php

namespace SociosEnInternet\PhpciPlugins;

use PHPCI\Plugin;
use PHPCI\Builder;
use PHPCI\Model\Build;
use PHPCI\Helper\Lang;

/**
* Drush Plugin - Provides access to Drush functionality.
* @author       Ivan Bustos <contacto@ivanbustos.com>
* @package      PHPCI
* @subpackage   Plugins
*/
class Drush implements Plugin
{
    protected $directory;
    protected $executable;
    protected $command;
    protected $phpci;

    /**
     * Set up the plugin, configure options, etc.
     * @param Builder $phpci
     * @param Build $build
     * @param array $options
     */
    public function __construct(Builder $phpci, Build $build, array $options = array())
    {
        $path             = $phpci->buildPath;
        $this->phpci      = $phpci;
        $this->build      = $build;
        $this->directory  = $path;
        $this->command     = '--version';
        
        if (array_key_exists('directory', $options)) {
            $this->directory = $options['directory'];
        }

        if (array_key_exists('command', $options)) {
            $this->command = $options['command'];
        }
        
        if (isset($options['executable'])) {
            $this->executable = $options['executable'];
        } else {
            $this->executable = $this->phpci->findBinary('drush');
        }
    }

    /**
    * Executes Drush and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        $pwdLocation = $this->phpci->findBinary(array('pwd'));
        $this->phpci->executeCommand($pwdLocation);
        $build_location = $this->phpci->getLastOutput();

        $cmd = 'cd ' . $build_location . '/' . $this->directory;
        $cmd .= ' && ' . $this->executable . ' -y ';
        $cmd .= $this->command;
        
        $this->phpci->executeCommand('/bin/echo "' . $cmd . '"');
        return $this->phpci->executeCommand($cmd);
    }
}
