<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocodeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('geocoder:geocode')
            ->setDescription('Geocode an address or a ip address')
            ->addArgument('address', InputArgument::REQUIRED, 'The address')
            ->addOption('provider', null, InputOption::VALUE_OPTIONAL)
            ->setHelp(<<<HELP
The <info>geocoder:geocoder</info> command will fetch the latitude
and longitude from the given address.

You can force a provider with the "provider" option.

<info>php app/console geocoder:geocoder "Eiffel Tower" --provider=yahoo</info>
HELP
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $geocoder \Geocoder\Geocoder */
        $geocoder = $this->getContainer()->get('bazinga_geocoder.geocoder');

        if ($input->getOption('provider')) {
            $geocoder->using($input->getOption('provider'));
        }

        $results = $geocoder->geocode($input->getArgument('address'));
        $data = $results->first()->toArray();

        $max = 0;

        foreach ($data as $key => $value) {
            $length = strlen($key);
            if ($max < $length) {
                $max = $length;
            }
        }

        $max += 2;

        foreach ($data as $key => $value) {
            $key = $this->humanize($key);

            $output->writeln(sprintf(
                '<comment>%s</comment>: %s',
                str_pad($key, $max, ' ', STR_PAD_RIGHT),
                is_array($value) ? json_encode($value) : $value
            ));
        }
    }

    private function humanize($text)
    {
        $text = preg_replace('/([A-Z][a-z]+)|([A-Z][A-Z]+)|([^A-Za-z ]+)/', ' \1', $text);

        return ucfirst(strtolower($text));
    }
}
