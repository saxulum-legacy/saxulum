<?php

namespace Application\Doctrine\Registry;

use Doctrine\Common\Persistence\ManagerRegistry as ManagerRegistryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\ORMException;

class ManagerRegistry implements ManagerRegistryInterface
{
    /**
     * @var string
     */
    protected $proxyInterfaceName;

    /**
     * @var Connection[]
     */
    protected $connections;

    /**
     * @var string
     */
    protected $defaultConnectionName;

    /**
     * @var ObjectManager[]
     */
    protected $managers;

    /**
     * @var string
     */
    protected $defaultManagerName;

    /**
     * @param \Pimple $container
     * @param string $proxyInterfaceName
     */
    public function __construct(\Pimple $container, $proxyInterfaceName = 'Doctrine\ORM\Proxy\Proxy')
    {
        $this->connections = $container['dbs'];
        $this->defaultConnectionName = $container['dbs.default'];
        $this->managers = $container['orm.ems'];
        $this->defaultManagerName = $container['orm.ems.default'];
        $this->proxyInterfaceName = $proxyInterfaceName;
    }

    /**
     * @return string
     */
    public function getDefaultConnectionName()
    {
        return $this->defaultConnectionName;
    }

    /**
     * @param string|null $name
     * @return Connection
     * @throws \InvalidArgumentException
     */
    public function getConnection($name = null)
    {
        if($name === null) {
            $name = $this->getDefaultConnectionName();
        }

        if (!isset($this->connections[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Connection named "%s" does not exist.', $name));
        }

        return $this->connections[$name];
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * @return array
     */
    public function getConnectionNames()
    {
        $names = array();
        foreach($this->getConnections() as $name => $connection) {
            $names[] = $name;
        }
        return $names;
    }

    /**
     * @return string
     */
    public function getDefaultManagerName()
    {
        return $this->defaultManagerName;
    }

    /**
     * @param null $name
     * @return ObjectManager
     * @throws \InvalidArgumentException
     */
    public function getManager($name = null)
    {
        if($name === null) {
            $name = $this->getDefaultManagerName();
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }

        return $this->managers[$name];
    }

    /**
     * @return ObjectManager[]
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * @param null $name
     * @return void
     * @throws \InvalidArgumentException
     */
    public function resetManager($name = null)
    {
        if (null === $name) {
            $name = $this->getDefaultManagerName();
        }

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf('Doctrine Manager named "%s" does not exist.', $name));
        }

        $this->managers[$name] = null;
    }

    /**
     * @param string $alias
     * @return string
     * @throws \Doctrine\ORM\ORMException
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
            }
        }

        throw ORMException::unknownEntityNamespace($alias);
    }

    /**
     * @return array
     */
    public function getManagerNames()
    {
        $names = array();
        foreach($this->getManagers() as $name => $manager) {
            $names[] = $name;
        }
        return $names;
    }

    /**
     * @param string $persistentObject
     * @param null $persistentManagerName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        return $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /**
     * @param string $class
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    public function getManagerForClass($class)
    {
        // Check for namespace alias
        if (strpos($class, ':') !== false) {
            list($namespaceAlias, $simpleClassName) = explode(':', $class);
            $class = $this->getAliasNamespace($namespaceAlias) . '\\' . $simpleClassName;
        }

        $proxyClass = new \ReflectionClass($class);
        if ($proxyClass->implementsInterface($this->proxyInterfaceName)) {
            $class = $proxyClass->getParentClass()->getName();
        }

        foreach ($this->getManagers() as $manager) {
            if (!$manager->getMetadataFactory()->isTransient($class)) {
                return $manager;
            }
        }
    }
}