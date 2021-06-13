<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2018 LYRASOFT.
 * @license    LGPL-2.0-or-later
 */

namespace Lyrasoft\Unidev\Controller;

use Windwalker\Legacy\Core\Controller\AbstractController;
use Windwalker\Legacy\Core\Controller\Middleware\JsonApiMiddleware;
use Windwalker\Legacy\Core\Controller\Middleware\JsonResponseMiddleware;
use Windwalker\Legacy\Core\Controller\Traits\CorsTrait;
use Windwalker\Legacy\Core\Controller\Traits\CsrfProtectionTrait;
use Windwalker\Legacy\Http\Response\HtmlResponse;
use Windwalker\Legacy\Http\Response\XmlResponse;
use Windwalker\Legacy\Router\Exception\RouteNotFoundException;
use Windwalker\Legacy\String\StringNormalise;
use Windwalker\Legacy\Utilities\Queue\PriorityQueue;
use Windwalker\Legacy\Utilities\Reflection\ReflectionHelper;

/**
 * The AbstractAjaxController class.
 *
 * @since  1.4
 */
class AbstractAjaxController extends AbstractController
{
    use CsrfProtectionTrait;
    use CorsTrait;

    /**
     * Property taskKey.
     *
     * @var  string
     */
    protected $taskKey = 'task';

    /**
     * prepareExecute
     *
     * @return  void
     *
     * @since  1.4
     */
    protected function prepareExecute()
    {
        parent::prepareExecute();

        switch (strtolower($this->input->get('format', 'json'))) {
            case 'xml':
                $this->response = new XmlResponse();
                break;

            case 'html':
                $this->response = new HtmlResponse();
                break;

            case 'raw':
                // No action
                break;

            case 'json':
            default:
                $this->addMiddleware(JsonResponseMiddleware::class, PriorityQueue::HIGH);
                $this->addMiddleware(JsonApiMiddleware::class, PriorityQueue::HIGH);
                break;
        }
    }

    /**
     * The main execution process.
     *
     * @return  mixed
     * @throws \ReflectionException
     * @throws \Windwalker\Legacy\DI\Exception\DependencyResolutionException
     * @throws \Throwable
     */
    protected function doExecute()
    {
        $taskName = $this->input->get($this->taskKey);

        $task = explode('.', $taskName, 2);

        if (isset($task[1])) {
            list($subModule, $t) = $task;

            $ns = ReflectionHelper::getNamespaceName(static::class) . '\\' .
                StringNormalise::toCamelCase($subModule) . '\\';
            $controller = $ns . $this->router->getRouter()->fetchControllerSuffix($this->input->getMethod());

            if (!class_exists($controller)) {
                throw new RouteNotFoundException(sprintf(
                    'AJAX Task: %s not found.' . only_debug(' Controller: ' . $controller . ' not exists.'),
                    $this->input->get->get($this->taskKey) ?: $taskName
                ));
            }

            $input = $this->input->toArray();
            $input[$this->taskKey] = $t;
            $input['format'] = 'raw';

            return $this->hmvc(
                $this->container->newInstance($controller),
                $input
            );
        }

        return $this->delegate(
            StringNormalise::toCamelCase($this->input->get($this->taskKey))
        );
    }
}
