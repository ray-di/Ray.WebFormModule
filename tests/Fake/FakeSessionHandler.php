<?php
/**
 * This file is taken from Aura.Session and modified.
 */

declare(strict_types=1);

namespace Ray\WebFormModule;

use SessionHandlerInterface;

// a session handler that does nothing, for testing purposes only
class FakeSessionHandler implements SessionHandlerInterface
{
    public string|null $data = null;

    public function close(): bool
    {
        return true;
    }

    public function destroy(string $id): bool
    {
        $this->data = null;

        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        return 0;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        return $this->data ?? '';
    }

    public function write(string $id, string $data): bool
    {
        $this->data = $data;

        return true;
    }
}
