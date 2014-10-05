<?php

namespace Rtdocs\Domain\Github;

use PDO;
use Guzzle\Http\Message\Response;
use Github\HttpClient\Cache\CacheInterface;
use Exception;

class DbCache implements CacheInterface
{
    /**
     * @var array
     */
    protected $row;

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
                $sql = 'INSERT INTO cache (id, content, etag, created, modified) VALUES (:id, :content, :etag, now(), now());';
            } else {
                $sql = 'UPDATE cache SET content = :content, etag = :etag, modified = now() WHERE id = :id';
            }
            unset($this->row[$bind['id']]);
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue('content', $bind['content']);
            $stmt->bindValue('etag', $bind['etag']);
            $stmt->bindValue('id', $bind['id']);
            $stmt->execute();
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
        $id = md5($id);
        if (isset($this->row[$id])) {
            return $this->row[$id];
        }
        try {
            $sql = "SELECT id, content, etag, created, modified FROM cache WHERE id =:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue('id', $id);
            $stmt->execute();
            $this->row[$id] = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (Exception $e) {
        }
        return $this->row[$id];
    }
}
