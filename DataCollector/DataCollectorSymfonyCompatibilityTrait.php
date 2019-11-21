<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\DataCollector;

use Bazinga\GeocoderBundle\Plugin\ProfilingPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

if (Kernel::VERSION_ID >= 40308) {
    trait DataCollectorSymfonyCompatibilityTrait
    {
        /**
         * {@inheritdoc}
         */
        public function collect(Request $request, Response $response, \Throwable $exception = null)
        {
            if (!empty($this->data['queries'])) {
                // To avoid collection more that once.
                return;
            }

            /** @var ProfilingPlugin[] $instances */
            $instances = $this->instances;

            foreach ($instances as $instance) {
                foreach ($instance->getQueries() as $query) {
                    $query['query'] = $this->cloneVar($query['query']);
                    $query['result'] = $this->cloneVar($query['result']);
                    $this->data['queries'][] = $query;
                }
            }
        }
    }
} else {
    trait DataCollectorSymfonyCompatibilityTrait
    {
        /**
         * {@inheritdoc}
         */
        public function collect(Request $request, Response $response, \Exception $exception = null)
        {
            if (!empty($this->data['queries'])) {
                // To avoid collection more that once.
                return;
            }

            /** @var ProfilingPlugin[] $instances */
            $instances = $this->instances;

            foreach ($instances as $instance) {
                foreach ($instance->getQueries() as $query) {
                    $query['query'] = $this->cloneVar($query['query']);
                    $query['result'] = $this->cloneVar($query['result']);
                    $this->data['queries'][] = $query;
                }
            }
        }
    }
}
