<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

/**
 * Backwards-compatible alias for {@see WebFormModule}.
 *
 * Use {@see WebFormModule} in new code. This class is kept so existing
 * applications that install `new AuraInputModule()` continue to work
 * without changes.
 */
class AuraInputModule extends WebFormModule
{
}
