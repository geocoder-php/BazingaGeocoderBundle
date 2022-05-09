<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * An abstract factory that makes it easier to implement new factories. A class that extend the AbstractFactory
 * should override AbstractFactory::$dependencies and AbstractFactory::configureOptionResolver().
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
abstract class AbstractFactory implements ProviderFactoryInterface
{
    /**
     * @var array<int, array{requiredClass: class-string, packageName: string}>
     */
    protected static array $dependencies = [];

    public function __construct(protected ?HttpClient $httpClient = null)
    {
    }

    /**
     * @phpstan-param array<mixed, mixed> $config
     */
    abstract protected function getProvider(array $config): Provider;

    /**
     * {@inheritdoc}
     */
    public function createProvider(array $options = []): Provider
    {
        self::verifyDependencies();

        $resolver = new OptionsResolver();
        static::configureOptionResolver($resolver);
        $config = $resolver->resolve($options);

        return $this->getProvider($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function validate(array $options, string $providerName): void
    {
        static::verifyDependencies();

        $resolver = new OptionsResolver();
        static::configureOptionResolver($resolver);

        try {
            $resolver->resolve($options);
        } catch (\Exception $e) {
            $message = sprintf(
                'Error while configure provider "%s". Verify your configuration at "bazinga_geocoder.providers.%s.options". %s',
                $providerName,
                $providerName,
                $e->getMessage()
            );

            throw new InvalidConfigurationException($message, $e->getCode(), $e);
        }
    }

    /**
     * Make sure that we have the required class and throw and exception if we don't.
     *
     * @throws \LogicException
     */
    protected static function verifyDependencies(): void
    {
        foreach (static::$dependencies as $dependency) {
            if (!class_exists($dependency['requiredClass'])) {
                throw new \LogicException(sprintf('You must install the "%s" package to use the "%s" factory.', $dependency['packageName'], static::class));
            }
        }
    }

    /**
     * By default we do not have any options to configure. A factory should override this function and configure
     * the options resolver.
     */
    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
    }
}
