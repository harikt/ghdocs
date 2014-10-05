<?php

namespace Rtdocs\Domain\Github;

use PDO;
use Guzzle\Http\Message\Response;
use Github\HttpClient\Cache\CacheInterface;
use Exception;

class DbCache implements CacheInterface
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @param string $db
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $row = $this->getRow($id);
        if ($row) {
            return unserialize($row['content']);
        }
        throw new \InvalidArgumentException('File not found');
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, Response $response)
    {
        $bind = array(
            'content' => serialize($response),
            'etag' => $response->getHeader('ETag'),
            'id' => md5($id)
        );
        try {
            if (! $this->getRow($id)) {
                $stmt = 'INSERT INTO cache (id, content, etag, created, modified) VALUES (:id, :content, :etag, now(), now());';
            } else {
                $stmt = 'UPDATE cache SET content = :content, etag = :etag, modified = now() WHERE id = :id';
            }
            $sth = $this->pdo->prepare($stmt);
            $sth->bindValue('content', $bind['content']);
            $sth->bindValue('etag', $bind['etag']);
            $sth->bindValue('id', $bind['id']);
            $sth->execute();
        } catch (Exception $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        $row = $this->getRow($id);
        if ($row) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedSince($id)
    {
        if ($this->has($id)) {
            $row = $this->getRow($id);
            return time($row['modified']);
        }
    }

    public function getETag($id)
    {
        $row = $this->getRow($id);
        if ($row) {
            return $row['etag'];
        }
    }

    /**
     * @param $id string
     *
     * @return array
     */
    protected function getRow($id)
    {
        $row = '';
        try {
            $id = md5($id);
            $stmt = "SELECT id, content, etag, created, modified FROM cache WHERE id =:id";
            $sth = $this->pdo->prepare($stmt);
            $sth->bindValue('id', $id);
            $sth->execute();
            $row = $sth->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // throw new \InvalidArgumentException($e->getMessage());
        }
        return $row;
    }
}
