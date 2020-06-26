<?php

declare(strict_types=1);

namespace CodeRhapsodie\CrCoreExtraBundle\Checker;

interface CheckerInterface
{
    public function check(array $files): array;
}
