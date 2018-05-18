<?php
/**
 * Created by PhpStorm.
 * User: fesiong
 * Date: 16/8/17
 * Time: 下午7:31
 */

namespace Fesion;

use Fesion\Library\Cookie;
use Fesion\Library\Error;
use Fesion\Library\Helper;
use Fesion\Library\XssHtml;
use Fesion\Models\Admin;
use Fesion\Models\Site;
use Fesion\Models\User;
use Phalcon\Config;
use Phalcon\Crypt;
use Phalcon\Exception;
use Phalcon\Filter;
use Phalcon\Loader;
use Phalcon\Security;
use Phalcon\Mvc\View;
use Fesion\Library\Tag;
use RuntimeException;
use Phalcon\Mvc\Micro as MvcMicro;
use Phalcon\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Queue\Beanstalk;
use Fesion\Library\Queue\DummyServer;
use Phalcon\Di\FactoryDefault;
use Phalcon\Flash\Direct as Flash;
use Fesion\Library\Url as UrlResolver;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\View\Exception as ViewException;
use Phalcon\Cache\Frontend\Output as FrontOutput;


class Fesion extends MvcMicro
{
    private $app;
    protected $di;
    protected $_noAuthPages;

    private $loaders = [
        'cache',
        'security',
        'database',
        'router',
        'dispatcher',
        'crypt',
        'filter',
    ];

    public function __construct()
    {
        $this->di = $di = new FactoryDefault;
        $this->_noAuthPages = [];

        $em = new EventsManager;
        $em->enablePriorities(true);

        $config = $this->initConfig($di);

        $di->setShared('config', $config);

        $this->initLoader($di, $config, $em);
        $this->initLogger($di, $config, $em);

        foreach ($this->loaders as $service) {
            $serviceName = ucfirst($service);
            $this->{'init' . $serviceName}($di, $config, $em);
        }

        $di->setShared('eventsManager', $em);
        $di->setShared('app', $this);
        $app = $this;
        $em->attach(
            'micro',
            function ($event, $app) use ($di) {
                if ($event->getType() == 'beforeExecuteRoute') {
                    $token = $this->request->get('token');
                    $admin = Admin::findFirstByToken($token);
                    if($admin){
                        $user_id   = $admin->uid;
                        $user_info = User::findFirstByUid($user_id);

                        $di->setShared('admin', function() use ($admin){
                            return $admin;
                        });
                        $di->setShared('user_info', function() use ($user_info){
                            return $user_info;
                        });
                        $di->setShared('user_id', function() use ($user_id){
                            return $user_id;
                        });
                    }

                    //unauthenticated
                    $currentRoute = parse_url($_SERVER['REQUEST_URI'])['path'];
                    if(!in_array($currentRoute, $app->getUnauthenticated()) && !$user_id){
                        Helper::json_output(1001, '用户未登录');
                    }
                }
            }
        );
        $this->setEventsManager($em);
    }

    public function getUnauthenticated()
    {
        return $this->_noAuthPages;
    }

    public function run()
    {
        $this->notFound(function () {
            throw new Exception('Page doesn\'t exist.');
        });
        $this->handle();
    }

    protected function initLogger(DiInterface $di, Config $config, EventsManager $em)
    {
        set_exception_handler(array('Fesion\Library\Error', 'handleException'));
        set_error_handler(array('Fesion\Library\Error', 'handleError'),E_ERROR & E_WARNING);
        register_shutdown_function(array('Fesion\Library\Error', 'handleShutdown'));
    }

    protected function initLoader(DiInterface $di, Config $config, EventsManager $em)
    {
        $loader = new Loader;
        $loader->registerNamespaces(
            [
                'Fesion'                   => $config->get('application')->appDir,
                'Fesion\Models'            => $config->get('application')->modelsDir,
                'Fesion\Controllers'       => $config->get('application')->controllersDir,
                'Fesion\Plugins'           => $config->get('application')->pluginsDir,
                'Fesion\Library'           => $config->get('application')->libraryDir,
                'Phalcon'                   => $config->get('application')->phalconDir,
            ]
        );

        $loader->setEventsManager($em);
        $loader->register();

        $di->setShared('loader', $loader);

        return $loader;
    }

    protected function initCache(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('modelsCache', function () use ($config) {
            $frontend = '\Phalcon\Cache\Frontend\\' . $config->get('modelsCache')->frontend;
            $frontend = new $frontend(['lifetime' => $config->get('modelsCache')->lifetime]);

            $config  = $config->get('modelsCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime'], $config['frontend']);

            return new $backend($frontend, $config);
        });

        $di->setShared('dataCache', function () use ($config) {
            $frontend = '\Phalcon\Cache\Frontend\\' . $config->get('dataCache')->frontend;
            $frontend = new $frontend(['lifetime' => $config->get('dataCache')->lifetime]);

            $config  = $config->get('dataCache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['backend'];
            unset($config['backend'], $config['lifetime'], $config['frontend']);

            return new $backend($frontend, $config);
        });
    }

    protected function initSecurity(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('security', function () {
            $security = new Security;
            $security->setWorkFactor(12);

            return $security;
        });
    }

    protected function initDatabase(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('db', function () use ($config, $em, $di) {
            $config  = $config->get('database')->toArray();
            $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
            unset($config['adapter']);

            /** @var \Phalcon\Db\Adapter\Pdo $connection */
            $connection = new $adapter($config);

            $em->attach(
                'db',
                function ($event, $connection) use ($di) {
                    /**
                     * @var \Phalcon\Events\Event $event
                     * @var \Phalcon\Db\AdapterInterface $connection
                     * @var \Phalcon\DiInterface $di
                     */
                    if ($event->getType() == 'beforeQuery') {
                        $variables = $connection->getSQLVariables();
                        $string    = $connection->getSQLStatement();

                        if ($variables) {
                            $string .= ' [' . join(',', $variables) . ']';
                        }

                        //todo log sql
                    }
                }
            );

            $connection->setEventsManager($em);
            $connection->query("SET sql_mode = ''");

            return $connection;
        });

        $di->setShared('modelsManager', function () use ($em) {
            $modelsManager = new ModelsManager;
            $modelsManager->setEventsManager($em);

            return $modelsManager;
        });

        $di->setShared('modelsMetadata', function () use ($config, $em) {
            $config = $config->get('metadata')->toArray();
            $adapter = '\Phalcon\Mvc\Model\Metadata\\' . $config['adapter'];
            unset($config['adapter']);

            $metaData = new $adapter($config);

            return $metaData;
        });
    }

    protected function initRouter(DiInterface $di, Config $config, EventsManager $em)
    {
        $routes = require_once APP_PATH . 'app/config/routes.php';
        foreach ($routes as $key => $obj) {
            $key = ucfirst($key);
            $control = 'Fesion\Controllers\\' . $key . 'Controller';
            $controllerName = class_exists($control) ? $control : false;
            if (!$controllerName) {
                throw new Exception("Wrong controller name in routes ({$control})");
            }
            $controller = new $controllerName;

            foreach ($obj as $route){
                // Which pages are allowed to skip authentication
                if (isset($route['authentication']) && $route['authentication'] === false) {
                    //route
                    $this->_noAuthPages[] = $route['route'];
                }
                $controllerAction = $route['action'] . 'Action';
                switch ($route['method']) {
                    case 'get':
                        $this->get($route['route'], [$controller, $controllerAction]);
                        break;
                    case 'post':
                        $this->post($route['route'], [$controller, $controllerAction]);
                        break;
                    case 'delete':
                        $this->delete($route['route'], [$controller, $controllerAction]);
                        break;
                    case 'put':
                        $this->put($route['route'], [$controller, $controllerAction]);
                        break;
                    case 'head':
                        $this->head($route['route'], [$controller, $controllerAction]);
                        break;
                    case 'options':
                        $this->options($route['route'], [$controller, $controllerAction]);
                        break;
                    case 'patch':
                        $this->patch($route['route'], [$controller, $controllerAction]);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    protected function initDispatcher(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('dispatcher', function () use ($em) {
            $dispatcher = new Dispatcher;

            $dispatcher->setDefaultNamespace('Fesion\Controllers');
            $dispatcher->setEventsManager($em);

            return $dispatcher;
        });
    }

    protected function initCrypt(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('crypt', function() use ($config) {
            $crypt = new Crypt();
            $crypt->setKey(CRYPT_KEY);

            return $crypt;
        });
    }

    protected function initConfig(DiInterface $di, $path = null)
    {
        $path = $path ?: APP_PATH . 'app/config/';

        if (!is_readable($path . 'config.php')) {
            throw new RuntimeException(
                '无法读取配置文件 ' . $path . 'config.php'
            );
        }

        $config = include $path . 'config.php';

        if (is_array($config)) {
            $config = new Config($config);
        }

        if (!$config instanceof Config) {
            $type = gettype($config);
            if ($type == 'boolean') {
                $type .= ($type ? ' (true)' : ' (false)');
            } elseif (is_object($type)) {
                $type = get_class($type);
            }

            throw new RuntimeException(
                sprintf(
                    '无法读取配置文件。 配置文件必须是一个数组或者继承自Phalcon\Config接口 %s',
                    $type
                )
            );
        }

        if (is_readable($path . APPLICATION_ENV . '.php')) {
            $override = include_once $path . APPLICATION_ENV . '.php';

            if (is_array($override)) {
                $override = new Config($override);
            }

            if ($override instanceof Config) {
                $config->merge($override);
            }
        }

        return $config;
    }

    public function initFilter(DiInterface $di, Config $config, EventsManager $em){
        $di->setShared('filter', function() {
            $filter = new Filter();

            $filter->add('xss', function ($value){
                $xssHtml = new XssHtml($value);
                return $xssHtml->getHtml();
            });

            return $filter;
        });
    }
}