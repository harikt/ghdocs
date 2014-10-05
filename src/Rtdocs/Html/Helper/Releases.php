<?php
namespace Rtdocs\Html\Helper;

use Aura\Html\Escaper;
use Aura\Html\Helper\Ul;
use Aura\Web\Request;
use Aura\Router\Router;
use Rtdocs\Domain\Github\FetchService;

class Releases
{
    protected $request;

    protected $router;

    protected $service;

    protected $ul;

    protected $escaper;

    public function __construct(Router $router, Request $request, FetchService $service, Ul $ul, Escaper $escaper)
    {
        $this->request = $request;
        $this->router = $router;
        $this->service = $service;
        $this->ul = $ul;
        $this->escaper = $escaper;
    }

    public function __invoke()
    {
        $repo = $this->request->params->get('repo');
        $org = $this->request->params->get('org');
        if ($repo && $org) {
            $file = $this->request->params->get('file');
            $version = $this->request->params->get('version');
            $payload = $this->service->getReleases($org, $repo);
            $releases = $payload->get('releases');
            $items = array();
            foreach ($releases as $release) {
                if ($release == $version) {
                    $items['<a href="' . $this->router->generateRaw('read', array(
                            'repo' => urlencode($repo),
                            'org' => urlencode($org),
                            'file' => $file,
                            'version' => urlencode($release),
                        )
                    ) . '">' . $this->escaper->html($release) . '</a>'] = array('class' => 'active');
                } else {
                    $items[] = '<a href="' . $this->router->generateRaw('read', array(
                            'repo' => urlencode($repo),
                            'org' => urlencode($org),
                            'file' => $file,
                            'version' => urlencode($release),
                        )
                    ) . '">' . $this->escaper->html($release) . '</a>';
                }
            }
            if (! empty($items)) {
                return $this->ul->__invoke(array('class' => "dropdown-menu", 'role' => "menu"))->rawItems($items);
            }
        }
        return '';
    }
}
