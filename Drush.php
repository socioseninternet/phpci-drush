<?php

namespace PHPCI\Plugin;

use PHPCI;
use PHPCI\Builder;
use PHPCI\Model\Build;
use PHPCI\Helper\Lang;

/**
* Drush Plugin - Provides access to Drush functionality.
* @author       Ivan Bustos <contacto@ivanbustos.com>
* @package      PHPCI
* @subpackage   Plugins
*/
class Drush implements PHPCI\Plugin, PHPCI\ZeroConfigPlugin
{
    protected $directory;
    protected $command;
    protected $phpci;

    /**
     * Check if this plugin can be executed.
     * @param $stage
     * @param Builder $builder
     * @param Build $build
     * @return bool
     */
    public static function canExecute($stage, Builder $builder, Build $build)
    {
        $path = $builder->buildPath . DIRECTORY_SEPARATOR . '/web/sites/default/settings.php';

        if (file_exists($path)) {
            return true;
        }

        return false;
    }

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
            $this->directory = $path . DIRECTORY_SEPARATOR . $options['directory'];
        }

        if (array_key_exists('command', $options)) {
            $this->command = $options['command'];
        }
    }

    /**
    * Executes Drush and runs a specified command (e.g. install / update)
    */
    public function execute()
    {
        $drushLocation = $this->phpci->findBinary(array('drush', 'drush.phar'));

        $cmd = '';

        if (IS_WIN) {
            $cmd = 'php ';
        }

        $cmd .= $drushLocation . ' -y ';
        $cmd .= ' --root="%s" %s';

        return $this->phpci->executeCommand($cmd, $this->directory, $this->command);
    }
}
