<?php
namespace Rtdocs\Web\Responder;

use Aura\View\View;
use Aura\Web\Response;
use Aura\Accept\Accept;
use Rtdocs\Domain\Payload\PayloadInterface;

abstract class AbstractResponder
{
    protected $accept;

    protected $available = array();

    protected $response;

    protected $payload;

    protected $payload_method = array();

    protected $view;

    public function __construct(
        Accept $accept,
        Response $response,
        View $view
    ) {
        $this->accept = $accept;
        $this->response = $response;
        $this->view = $view;
        $this->init();
    }

    protected function init()
    {
        if (! isset($this->payload_method['Domain\Payload\Error'])) {
            $this->payload_method['Domain\Payload\Error'] = 'error';
        }
    }

    public function __invoke()
    {
        $class = get_class($this->payload);
        $method = isset($this->payload_method[$class])
                ? $this->payload_method[$class]
                : 'notRecognized';
        $this->$method();
        return $this->response;
    }

    public function setPayload(PayloadInterface $payload)
    {
        $this->payload = $payload;
    }

    protected function notRecognized()
    {
        $domain_status = $this->payload->get('status');
        $this->response->status->set('500');
        $this->response->content->set("Unknown domain payload status: '$status'");
        return $this->response;
    }

    protected function negotiateMediaType()
    {
        if (! $this->available || ! $this->accept) {
            return true;
        }

        $available = array_keys($this->available);
        $media = $this->accept->negotiateMedia($available);
        if (! $media) {
            $this->response->status->set(406);
            $this->response->content->setType('text/plain');
            $this->response->content->set(implode(',', $available));
            return false;
        }

        $this->response->content->setType($media->getValue());
        return true;
    }

    protected function renderView($view, $layout = null)
    {
        $content_type = $this->response->content->getType();
        if ($content_type) {
            $view .= $this->available[$content_type];
        }

        $this->view->setView($view);
        $this->view->setLayout($layout);
        $this->view->addData($this->payload->get());
        $this->response->content->set($this->view->__invoke());
    }

    protected function notFound()
    {
        $this->response->status->set(404);
    }

    protected function error()
    {
        $e = $this->payload->getException();
        $this->response->status->set('500');
        $this->response->content->set($e->getMessage());
    }
}
