<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    // يتحقق من عمل بيئة PHPUnit الأساسية دون الاعتماد على قاعدة البيانات.
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
