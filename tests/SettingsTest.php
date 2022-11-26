<?php

declare(strict_types=1);

use App\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function testGet()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Settings key "42" does not exists.');

        Settings::get('42');
    }
}
