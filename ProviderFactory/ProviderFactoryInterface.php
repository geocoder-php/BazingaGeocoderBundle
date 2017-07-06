<?php

/*
 * This file is part of php-cache organization.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bazinga\Bundle\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\Provider;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface ProviderFactoryInterface
{
    /**
     * @param array $options
     *
     * @return Provider
     */
    public function createProvider(array $options = []);

    /**
     * Make sure the options are valid and the dependencies are met.
     *
     * @param array  $options      the options the user has provided
     * @param string $providerName the name the user has chosen for this provider
     *
     * @throws \LogicException                                                        If the factory has missing dependencies
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException If an option name is undefined
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException   If an option doesn't fulfill the specified validation rules
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException   If a required option is missing
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException If there is a cyclic dependency between lazy options and/or normalizers
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException     If a lazy option reads an unavailable option
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException           If called from a lazy option or normalizer
     */
    public static function validate(array $options, $providerName);
}
