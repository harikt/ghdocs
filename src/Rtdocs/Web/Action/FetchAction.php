<?php
namespace Rtdocs\Web\Action;

use Aura\Web\Request;
use Rtdocs\Web\Responder\FetchResponder;
use Rtdocs\Domain\Github\FetchService;

class FetchAction
{
    protected $service;

    protected $responder;

    protected $request;

    public function __construct(Request $request, FetchResponder $responder, FetchService $service)
    {
        $this->request = $request;
        $this->responder = $responder;
        $this->service = $service;
    }

    public function __invoke()
    {
        $repo = $this->request->params->get('repo');
        $org = $this->request->params->get('org');
        $file = $this->request->params->get('file');
        $version = $this->request->params->get('version');
        $payload = $this->service->readFile($org, $repo, $file, $version);
        $this->responder->setPayload($payload);
        return $this->responder;
    }
}
