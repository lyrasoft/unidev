<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2019 ${ORGANIZATION}.
 * @license    __LICENSE__
 */

namespace Lyrasoft\Unidev\Controller;

use Windwalker\Core\Controller\AbstractController;
use Windwalker\String\StringNormalise;

/**
 * The AbstractMultiTaskController class.
 *
 * @since  1.5.8
 */
class AbstractMultiTaskController extends AbstractController
{
    /**
     * Property taskKey.
     *
     * @var  string
     */
    protected $taskKey = 'task';

    /**
     * Property defaultTask.
     *
     * @var  string
     */
    protected $defaultTask = 'handle';

    /**
     * The main execution process.
     *
     * @return  mixed
     * @throws \ReflectionException
     * @throws \Windwalker\DI\Exception\DependencyResolutionException
     * @throws \Throwable
     */
    protected function doExecute()
    {
        return $this->delegate(
            StringNormalise::toCamelCase($this->input->get($this->taskKey, $this->defaultTask))
        );
    }
}
