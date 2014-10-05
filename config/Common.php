<?php
namespace Aura\Framework_Project\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Common extends Config
{
    public function define(Container $di)
    {
        $di->set('aura/project-kernel:logger', $di->lazyNew('Psr\Log\NullLogger'));
        $di->params['Rtdocs\Web\Action\FetchAction']['request'] = $di->lazyGet('aura/web-kernel:request');
        $di->params['Rtdocs\Web\Action\FetchAction']['service'] = $di->lazyGet('github_service');
        $di->params['Rtdocs\Web\Responder\FetchResponder']['response'] = $di->lazyGet('aura/web-kernel:response');
        $di->params['Rtdocs\Domain\Github\FetchService']['client'] = $di->lazyGet('github_client');
        $di->params['Rtdocs\Domain\Github\FetchService']['factory'] = $di->lazyNew('Rtdocs\Domain\Payload\PayloadFactory');

        $di->set('github_service', $di->lazyNew('Rtdocs\Domain\Github\FetchService'));

        $di->params['Aura\Asset_Bundle\AssetService']['map']['rtdocs/reader'] = dirname(__DIR__) . '/src/web';
        $di->params['Rtdocs\Html\Helper\Router']['router'] = $di->lazyGet('aura/web-kernel:router');
        $di->params['Aura\Html\HelperLocator']['map']['router'] = $di->lazyNew('Rtdocs\Html\Helper\Router');

        $di->params['Rtdocs\Html\Helper\Releases']['request'] = $di->lazyGet('aura/web-kernel:request');
        $di->params['Rtdocs\Html\Helper\Releases']['router'] = $di->lazyGet('aura/web-kernel:router');
        $di->params['Rtdocs\Html\Helper\Releases']['service'] = $di->lazyGet('github_service');
        $di->params['Rtdocs\Html\Helper\Releases']['ul'] = $di->lazyNew('Aura\Html\Helper\Ul');
        $di->params['Rtdocs\Html\Helper\Releases']['escaper'] = $di->lazyGet('aura/html:escaper');
        $di->params['Aura\Html\HelperLocator']['map']['releases'] = $di->lazyNew('Rtdocs\Html\Helper\Releases');
        $di->set('view', $di->lazyNew('Aura\View\View'));

        $di->params['Github\Client']['httpClient'] = $di->lazyNew('Github\HttpClient\CachedHttpClient');
        $di->setter['Github\HttpClient\CachedHttpClient']['setCache'] = $di->lazyNew('Rtdocs\Domain\Github\DbCache');
        $di->set('github_client', $di->lazyNew('Github\Client'));
        $di->params['Rtdocs\Domain\Github\DbCache']['pdo'] = $di->lazyGet('connection');
        $di->params['Pdo'] = array(
            'dsn' => getenv('DB_CONNECTION')
        );
        $di->set('connection', $di->lazyNew('Pdo'));
    }

    public function modify(Container $di)
    {
        $this->modifyLogger($di);
        $this->modifyCliDispatcher($di);
        $this->modifyWebRouter($di);
        $this->modifyWebDispatcher($di);
    }

    protected function modifyLogger(Container $di)
    {
        $project = $di->get('project');
        $mode = $project->getMode();
        $logger = $di->get('aura/project-kernel:logger');
    }

    protected function modifyCliDispatcher(Container $di)
    {
        $context = $di->get('aura/cli-kernel:context');
        $stdio = $di->get('aura/cli-kernel:stdio');
        $logger = $di->get('aura/project-kernel:logger');
        $dispatcher = $di->get('aura/cli-kernel:dispatcher');
        $dispatcher->setObject(
            'hello',
            function ($name = 'World') use ($context, $stdio, $logger) {
                $stdio->outln("Hello {$name}!");
                $logger->debug("Said hello to '{$name}'");
            }
        );
    }

    public function modifyWebRouter(Container $di)
    {
        $router = $di->get('aura/web-kernel:router');

        $router->add('hello', '/')
               ->setValues(array('action' => 'hello'));

        $router->add('read', '/{org}/{repo}/{version}/{file}')
            ->addTokens(array(
                'file' => '(.*)'
            ));
    }

    public function modifyWebDispatcher($di)
    {
        $dispatcher = $di->get('aura/web-kernel:dispatcher');
        $dispatcher->setObject('hello', function () use ($di) {
            $view = $di->get('view');
            $view_registry = $view->getViewRegistry();
            $view_registry->set(
                'hello',
                dirname(__DIR__) . "/src/Rtdocs/Web/Responder/views/hello.php"
            );
            $layout_registry = $view->getLayoutRegistry();
            $layout_registry->set(
                'default',
                dirname(__DIR__) . "/src/Rtdocs/Web/Responder/layouts/default.php"
            );
            $view->setView('hello');
            $view->setLayout('default');
            $response = $di->get('aura/web-kernel:response');
            $response->content->set($view->__invoke());
        });

        $dispatcher->setObject('read', $di->lazyNew('Rtdocs\Web\Action\FetchAction'));
    }
}
