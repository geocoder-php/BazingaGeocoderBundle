<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle;

use Geocoder\Dumper\Dumper;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DumperManager
{
    /**
     * @var array
     */
    private $dumpers;

    /**
     * Constructor.
     *
     * @param array $dumpers
     */
    public function __construct(array $dumpers = array())
    {
        $this->dumpers = array();

        foreach ($dumpers as $name => $dumper) {
            $this->set($name, $dumper);
        }
    }

    /**
     * Get a dumper.
     *
     * @param string $name The name of the dumper
     *
     * @return Dumper
     *
     * @throws \RuntimeException If no dumper was found
     */
    public function get($name)
    {
        if (!isset($this->dumpers[$name])) {
            throw new \RuntimeException(sprintf('The dumper "%s" does not exist', $name));
        }

        return $this->dumpers[$name];
    }

    /**
     * Sets a dumper.
     *
     * @param string $name   The name
     * @param Dumper $dumper The dumper instance
     */
    public function set($name, Dumper $dumper)
    {
        $this->dumpers[$name] = $dumper;
    }

    /**
     * Remove a dumper instance from the manager.
     *
     * @param string $name The name of the dumper
     *
     * @throws \RuntimeException If no dumper was found
     */
    public function remove($name)
    {
        if (!isset($this->dumpers[$name])) {
            throw new \RuntimeException(sprintf('The dumper "%s" does not exist', $name));
        }

        unset($this->dumpers[$name]);
    }

    /**
     * Return true if $name exists, false otherwise.
     *
     * @param $name The name of the dumper
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->dumpers);
    }
}
