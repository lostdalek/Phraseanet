<?php

namespace Proxies\__CG__\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Feed extends \Entities\Feed implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setIsPublic($public)
    {
        $this->__load();
        return parent::setIsPublic($public);
    }

    public function isPublic()
    {
        $this->__load();
        return parent::isPublic();
    }

    public function setIconUrl($iconUrl)
    {
        $this->__load();
        return parent::setIconUrl($iconUrl);
    }

    public function getIconUrl()
    {
        $this->__load();
        return parent::getIconUrl();
    }

    public function setBaseId($baseId)
    {
        $this->__load();
        return parent::setBaseId($baseId);
    }

    public function getBaseId()
    {
        $this->__load();
        return parent::getBaseId();
    }

    public function setTitle($title)
    {
        $this->__load();
        return parent::setTitle($title);
    }

    public function getTitle()
    {
        $this->__load();
        return parent::getTitle();
    }

    public function addPublisher(\Entities\FeedPublisher $publishers)
    {
        $this->__load();
        return parent::addPublisher($publishers);
    }

    public function removePublisher(\Entities\FeedPublisher $publishers)
    {
        $this->__load();
        return parent::removePublisher($publishers);
    }

    public function getPublishers()
    {
        $this->__load();
        return parent::getPublishers();
    }

    public function addEntry(\Entities\FeedEntry $entries)
    {
        $this->__load();
        return parent::addEntry($entries);
    }

    public function removeEntry(\Entities\FeedEntry $entries)
    {
        $this->__load();
        return parent::removeEntry($entries);
    }

    public function getEntries($offset_start = 0, $how_many = NULL)
    {
        $this->__load();
        return parent::getEntries($offset_start, $how_many);
    }

    public function getOwner()
    {
        $this->__load();
        return parent::getOwner();
    }

    public function isOwner(\User_Adapter $user)
    {
        $this->__load();
        return parent::isOwner($user);
    }

    public function getCollection(\Alchemy\Phrasea\Application $app)
    {
        $this->__load();
        return parent::getCollection($app);
    }

    public function setCollection(\collection $collection = NULL)
    {
        $this->__load();
        return parent::setCollection($collection);
    }

    public function setCreatedOn($createdOn)
    {
        $this->__load();
        return parent::setCreatedOn($createdOn);
    }

    public function getCreatedOn()
    {
        $this->__load();
        return parent::getCreatedOn();
    }

    public function setUpdatedOn($updatedOn)
    {
        $this->__load();
        return parent::setUpdatedOn($updatedOn);
    }

    public function getUpdatedOn()
    {
        $this->__load();
        return parent::getUpdatedOn();
    }

    public function isPublisher(\User_Adapter $user)
    {
        $this->__load();
        return parent::isPublisher($user);
    }

    public function getPublisher(\User_Adapter $user)
    {
        $this->__load();
        return parent::getPublisher($user);
    }

    public function setSubtitle($subtitle)
    {
        $this->__load();
        return parent::setSubtitle($subtitle);
    }

    public function getSubtitle()
    {
        $this->__load();
        return parent::getSubtitle();
    }

    public function isAggregated()
    {
        $this->__load();
        return parent::isAggregated();
    }

    public function getCountTotalEntries()
    {
        $this->__load();
        return parent::getCountTotalEntries();
    }

    public function hasAccess(\User_Adapter $user, \Alchemy\Phrasea\Application $app)
    {
        $this->__load();
        return parent::hasAccess($user, $app);
    }

    public function addToken(\Entities\FeedToken $tokens)
    {
        $this->__load();
        return parent::addToken($tokens);
    }

    public function removeToken(\Entities\FeedToken $tokens)
    {
        $this->__load();
        return parent::removeToken($tokens);
    }

    public function getTokens()
    {
        $this->__load();
        return parent::getTokens();
    }

    public function addEntrie(\Entities\FeedEntry $entries)
    {
        $this->__load();
        return parent::addEntrie($entries);
    }

    public function removeEntrie(\Entities\FeedEntry $entries)
    {
        $this->__load();
        return parent::removeEntrie($entries);
    }

    public function hasPage($pageNumber, $nbEntriesByPage)
    {
        $this->__load();
        return parent::hasPage($pageNumber, $nbEntriesByPage);
    }

    public function isAccessible(\User_Adapter $user, \Alchemy\Phrasea\Application $app)
    {
        $this->__load();
        return parent::isAccessible($user, $app);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'public', 'iconUrl', 'baseId', 'title', 'subtitle', 'createdOn', 'updatedOn', 'publishers', 'entries', 'tokens');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}