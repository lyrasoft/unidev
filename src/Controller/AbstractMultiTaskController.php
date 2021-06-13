<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Controller;

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Attributes\TaskMapping;
use Windwalker\DI\Attributes\Inject;
use Windwalker\DI\Container;
use Windwalker\Http\Request\ServerRequest;
use Windwalker\Legacy\IO\PsrInput;
use Windwalker\Utilities\StrNormalize;

/**
 * The AbstractMultiTaskController class.
 *
 * @since  1.5.8
 */
#[Controller]
#[TaskMapping(
    methods: [
        '*' => 'index'
    ]
)]
class AbstractMultiTaskController
{
    #[Inject]
    protected Container $container;

    public function index(AppContext $app)
    {
        $this->input = $app->service(PsrInput::class);

        $task = $app->input('task');
        StrNormalize::toCamelCase($task);
        return $this->container->call([$this, $task]);
    }
}
