<?php

declare(strict_types=1);

namespace App\Service\Handler;

abstract class AbstractHandlerService
{
    /**
     * @param mixed $model
     * @param OutputInterface $output
     */
    abstract protected function handle($model, $output): void;
}
