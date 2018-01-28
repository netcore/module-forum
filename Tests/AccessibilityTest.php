<?php

namespace Modules\Forum\Tests;

use Modules\Category\Models\Category;
use Tests\TestCase;

class AccessibilityTest extends TestCase
{
    public function testUserIsAbleToCreateThreadInCategory()
    {
        $category = Category::leaves()->first();

        $this->assertInstanceOf(Category::class, $category);
    }
}