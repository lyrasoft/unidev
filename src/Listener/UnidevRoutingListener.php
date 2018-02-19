<?php
/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Listener;

use Lyrasoft\Unidev\Helper\UnidevHelper;
use Lyrasoft\Unidev\UnidevPackage;
use Windwalker\Core\Router\MainRouter;
use Windwalker\Core\Router\RestfulRouter;
use Windwalker\Event\Event;
use Windwalker\Router\Route;
use Windwalker\Utilities\ArrayHelper;

/**
 * The UnidevListener class.
 *
 * @since  1.0
 */
class UnidevRoutingListener
{
    /**
     * Property unidev.
     *
     * @var  UnidevPackage
     */
    protected $unidev;

    /**
     * UnidevListener constructor.
     *
     * @param UnidevPackage $unidev
     */
    public function __construct(UnidevPackage $unidev = null)
    {
        $this->unidev = $unidev ?: UnidevHelper::getPackage();
    }

    /**
     * onRouterBeforeRouteMatch
     *
     * @param Event $event
     *
     * @return  void
     */
    public function onRouterBeforeRouteMatch(Event $event)
    {
        /** @var MainRouter $router */
        $router = $event['router'];

        $routing = $this->unidev->loadRouting();

        foreach ($routing as $name => $route) {
            $name = $this->unidev->name . '@' . $name;

            $pattern      = ArrayHelper::getValue($route, 'pattern');
            $variables    = ArrayHelper::getValue($route, 'variables', []);
            $allowMethods = ArrayHelper::getValue($route, 'method', []);

            if (isset($route['controller'])) {
                $route['extra']['controller'] = $route['controller'];
            }

            if (isset($route['action'])) {
                $route['extra']['action'] = $route['action'];
            }

            if (isset($route['hook'])) {
                $route['extra']['hook'] = $route['hook'];
            }

            $route['extra']['package'] = $this->unidev->name;

            $router->addRoute(new Route($name, $pattern, $variables, $allowMethods, $route));
        }
    }
}
