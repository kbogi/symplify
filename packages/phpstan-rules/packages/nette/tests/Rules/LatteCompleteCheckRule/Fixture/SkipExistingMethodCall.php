<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\Fixture;

use Nette\Application\UI\Control;
use Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\Source\SomeTypeWithMethods;

final class SkipExistingMethodCall extends Control
{
    public function render()
    {
        $someType = new SomeTypeWithMethods();

        $this->template->render(__DIR__ . '/../Source/existing_method_call.latte', [
            'someType' => $someType,
        ]);
    }

}
