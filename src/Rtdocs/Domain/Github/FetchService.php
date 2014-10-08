<?php
namespace Rtdocs\Domain\Github;

use Exception;
use Github\Client as GithubClient;
use Github\Exception\ApiLimitExceedException;
use ParsedownExtra;
use Rtdocs\Domain\Payload\PayloadFactory;

class FetchService
{
    protected $factory;

    public function __construct(PayloadFactory $factory, GithubClient $client, $client_id = null, $secret = null, $method = null)
    {
        $this->factory = $factory;
        $this->client = $client;
        if ($client_id && $method) {
            $this->client->authenticate($client_id, $secret, $method);
        }
    }

    public function readFile($org, $repo, $file, $version)
    {
        try {
            $readme = $this->client->repo()->contents()->show($org, $repo, $file, $version);
            $instance = new ParsedownExtra();
            $content = $instance->text(base64_decode($readme['content']));
            return $this->factory->found(array('content' => $content));
        } catch (ApiLimitExceedException $e) {
            return $this->factory->apiLimitExceed(array('content' => "Api limit exceeded"));
        } catch (Exception $e) {
            return $this->factory->notFound(array('content' => "Not found"));
        }
    }

    public function getReleases($org, $repo)
    {
        $releases = array();
        try {
            $response = $this->client->repo()->releases()->all($org, $repo);
            foreach ($response as $release) {
                $releases[] = $release['tag_name'];
            }
            return $this->factory->found(array('releases' => $releases));
        } catch (Exception $e) {
            return $this->factory->notFound(array('releases' => $releases));
        }
    }

    public function getNavigation($org, $repo, $version)
    {
        try {
            $file = $this->client->repo()->contents()->show($org, $repo, 'navigation.html', $version);
            return $file;
        } catch (Exception $e) {
        }
        return '';
    }
}
