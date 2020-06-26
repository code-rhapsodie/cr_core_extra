<?php

declare(strict_types=1);

namespace CodeRhapsodie\CrCoreExtraBundle;

use CodeRhapsodie\CrCoreExtraBundle\DependencyInjection\CrCoreExtraExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
class CrCoreExtraBundle extends Bundle
{
    protected $name = 'CrCoreExtraBundle';

    public function getContainerExtension()
    {
        return new CrCoreExtraExtension();
    }
}
