<?php

namespace Proxies\__CG__\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class FeedEntry extends \Entities\FeedEntry implements \Doctrine\ORM\Proxy\Proxy
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

    public function setAuthorName($authorName)
    {
        $this->__load();
        return parent::setAuthorName($authorName);
    }

    public function getAuthorName()
    {
        $this->__load();
        return parent::getAuthorName();
    }

    public function setAuthorEmail($authorEmail)
    {
        $this->__load();
        return parent::setAuthorEmail($authorEmail);
    }

    public function getAuthorEmail()
    {
        $this->__load();
        return parent::getAuthorEmail();
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

    public function addItem(\Entities\FeedItem $items)
    {
        $this->__load();
        return parent::addItem($items);
    }

    public function removeItem(\Entities\FeedItem $items)
    {
        $this->__load();
        return parent::removeItem($items);
    }

    public function getItems()
    {
        $this->__load();
        return parent::getItems();
    }

    public function setPublisher(\Entities\FeedPublisher $publisher = NULL)
    {
        $this->__load();
        return parent::setPublisher($publisher);
    }

    public function getPublisher()
    {
        $this->__load();
        return parent::getPublisher();
    }

    public function setFeed(\Entities\Feed $feed = NULL)
    {
        $this->__load();
        return parent::setFeed($feed);
    }

    public function getFeed()
    {
        $this->__load();
        return parent::getFeed();
    }

    public function isPublisher(\User_Adapter $user)
    {
        $this->__load();
        return parent::isPublisher($user);
    }

    public function getItem($id)
    {
        $this->__load();
        return parent::getItem($id);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'title', 'subtitle', 'authorName', 'authorEmail', 'createdOn', 'updatedOn', 'items', 'publisher', 'feed');
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