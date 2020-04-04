<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Client\Elastic;

use Symfony\Component\Messenger\Envelope;

interface ElasticClientInterface
{
    public function post(Envelope $envelope, string $index): ?array;
}
