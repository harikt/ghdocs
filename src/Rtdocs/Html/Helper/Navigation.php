<?php
namespace Rtdocs\Html\Helper;

use Aura\Web\Request;
use Aura\Router\Router;
use Rtdocs\Domain\Github\FetchService;
use Aura\Html\Escaper;

class Navigation
{
    protected $request;

    protected $service;

    protected $router;

    public function __construct(Router $router, Request $request, FetchService $service)
    {
        $this->request = $request;
        $this->service = $service;
        $this->router = $router;
    }

    public function __invoke()
    {
        $repo = $this->request->params->get('repo');
        $org = $this->request->params->get('org');
        $version = $this->request->params->get('version');
        if ($repo && $org && $version) {
            $file = $this->service->getNavigation($org, $repo, $version);
            if ($file) {
                $pattern = "/(?<=href=(\"|'))[^\"']+(?=(\"|'))/";
                $replacement = $this->router->generate(
                        'read',
                        array(
                            'repo' => $repo,
                            'org' => $org,
                            'version' => $version
                        )
                    ) . '/$0';
                $content = base64_decode($file['content']);
                $content = preg_replace($pattern, $replacement, $content);
                return $content;
            }
        }
        return '';
    }
}
