<?php

declare(strict_types=1);

namespace Nsbx\Bundle\ModelConstructorBundle;

interface ModelInterface
{
    public function getMapping(): array;
}
