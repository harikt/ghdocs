<?php
namespace Rtdocs\Web\Responder;

use Aura\Web\Response;
use Aura\View\View;

class FetchResponder extends AbstractResponder
{
    protected $response;

    protected $view;

    protected function init()
    {
        $view_names = array(
            'read',
            'notfound'
        );

        $view_registry = $this->view->getViewRegistry();
        foreach ($view_names as $name) {
            $view_registry->set(
                $name,
                __DIR__ . "/views/{$name}.php"
            );
        }

        $layout_names = array(
            'default',
            'notfound'
        );

        $layout_registry = $this->view->getLayoutRegistry();
        foreach ($layout_names as $name) {
            $layout_registry->set(
                $name,
                __DIR__ . "/layouts/{$name}.php"
            );
        }
    }

    protected $available = array(
        'text/html' => '',
        'application/json' => '.json'
    );

    protected $payload_method = array(
        'Rtdocs\Domain\Payload\Found' => 'found',
        'Rtdocs\Domain\Payload\NotFound' => 'notFound',
        'Rtdocs\Domain\Payload\ApiLimitExceed' => 'apiLimitExceed',
    );

    protected function found()
    {
        if ($this->negotiateMediaType()) {
            $this->response->cache->setControl(array('public' => true, 'max-age' => 43200));
            // $this->response->cache->setPublic();
            // $this->response->cache->setAge(43200);
            $this->renderView('read', 'default');
        }
    }

    protected function notFound()
    {
        $this->renderView('notfound', 'default');
    }

    protected function apiLimitExceed()
    {
        $this->renderView('limitexceed', 'default');
    }
}
