<?php

namespace tests;

use App\Settings;
use Exception;
use PHPUnit\Framework\TestCase;

final class SettingsTest extends TestCase
{
    public function testGet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Settings key "42" does not exists.');

        Settings::get('42');
    }
}
