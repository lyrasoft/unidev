<?php
/**
 * Part of unidev project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Lyrasoft\Unidev\Provider;

use Aws\CommandInterface;
use Aws\Credentials\Credentials;
use Aws\Middleware;
use Aws\S3\S3Client;
use Composer\CaBundle\CaBundle;
use Faker\Generator;
use Lyrasoft\Unidev\Captcha\CaptchaService;
use Lyrasoft\Unidev\Faker\UnidevFakerProvider;
use Lyrasoft\Unidev\Image\ImageUploaderFactory;
use Lyrasoft\Unidev\S3\S3Service;
use Windwalker\Core\Application\WindwalkerApplicationInterface;
use Windwalker\Core\Renderer\RendererManager;
use Windwalker\Core\Router\MainRouter;
use Windwalker\Core\Seeder\FakerService;
use Windwalker\DI\Container;
use Windwalker\DI\ServiceProviderInterface;
use Windwalker\Event\Event;
use Windwalker\Filesystem\Path;
use Windwalker\Utilities\Queue\PriorityQueue;

/**
 * The UnidevProvider class.
 *
 * @since  1.0
 */
class UnidevProvider implements ServiceProviderInterface
{
    /**
     * Property app.
     *
     * @var  WindwalkerApplicationInterface
     */
    protected $app;

    /**
     * UnidevProvider constructor.
     *
     * @param WindwalkerApplicationInterface $app
     */
    public function __construct(WindwalkerApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container $container The DI container.
     *
     * @return  void
     */
    public function register(Container $container)
    {
        $container = $container->getParent();

        // Old S3
        $container->share(\S3::class, static function (Container $container) {
            $config = $container->get('config');

            $endpoint = $config->get('unidev.amazon.endpoint', 's3.amazonaws.com');

            return new \S3($config->get('unidev.amazon.key'), $config->get('unidev.amazon.secret'), false, $endpoint);
        })->alias('unidev.storage.s3-legacy', \S3::class);

        // AWS S3 SDK
        $container->share(S3Client::class, static function (Container $container) {
            $config = $container->get('config');

            $credentials = new Credentials($config->get('unidev.amazon.key'), $config->get('unidev.amazon.secret'));

            $s3 = new S3Client([
                'credentials' => $credentials,
                'version' => 'latest',
                'region' => $config->get('unidev.amazon.region') ?: 'ap-northeast-1',
                'endpoint' => $config->get('unidev.amazon.endpoint') ?: 'https://s3.amazonaws.com',
                'http' => [
                    'verify' => CaBundle::getBundledCaBundlePath()
                ]
            ]);

            $s3->getHandlerList()->appendInit(
                Middleware::mapCommand(static function (CommandInterface $command) use ($config) {
                    if (!isset($command['Bucket'])) {
                        $command['Bucket'] = $config->get('unidev.amazon.bucket');
                    }

                    if (isset($command['Key'])) {
                        $command['Key'] = ltrim(Path::clean(
                            $config->get('unidev.amazon.subfolder') . '/' . $command['Key'],
                            '/'
                        ), '/');
                    }

                    return $command;
                })
            );

            return $s3;
        })->alias('unidev.storage.s3', S3Client::class);

        $container->prepareSharedObject(S3Service::class);

        // Imgur
        $container->prepareSharedObject(
            \Imgur\Client::class,
            static function (\Imgur\Client $client, Container $container) {
                $config = $container->get('config');
                $client->setOption('client_id', $config->get('unidev.imgur.key'));
                $client->setOption('client_secret', $config->get('unidev.imgur.secret'));

                return $client;
            }
        )->alias('unidev.storage.imgur', \Imgur\Client::class);

        // Image Uploader
        $container->share('unidev.image.uploader.factory', static function (Container $container) {
            return new ImageUploaderFactory($container);
        });

        // Uploader
        $container->share('unidev.image.uploader', static function (Container $container) {
            return $container->get('unidev.image.uploader.factory')->create();
        });

        // Faker
        $container->extend(FakerService::class, static function (FakerService $fakerService) {
            $fakerService->getDispatcher()->listen('afterFakerCreated', function (Event $event) {
                /** @var Generator $faker */
                $faker = $event['faker'];
                $faker->addProvider(new UnidevFakerProvider($faker));
            });

            return $fakerService;
        });

        if ($this->app->isConsole()) {
            return;
        }

        // Add global paths
        $container->extend(RendererManager::class, static function (RendererManager $manager) {
            $manager->addGlobalPath(UNIDEV_ROOT . '/Resources/templates', PriorityQueue::LOW - 30);

            return $manager;
        });

        // Add AJAX methods to router
        $container->extend(MainRouter::class, static function (MainRouter $router) {
            $router->setHttpMethodSuffix('AJAX_GET', 'AjaxGetController');
            $router->setHttpMethodSuffix('AJAX_POST', 'AjaxSaveController');

            return $router;
        });

        // Captcha
        $container->prepareSharedObject(CaptchaService::class);
    }
}
