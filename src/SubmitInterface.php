<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

interface SubmitInterface
{
    /**
     * Return subject value
     *
     * @return array<string, mixed>|object
     */
    public function submit();
}
