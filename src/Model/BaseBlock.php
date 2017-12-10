<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BlockBundle\Model;

/**
 * Base abstract Block class that provides a default implementation of the block interface.
 */
abstract class BaseBlock implements BlockInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var BlockInterface
     */
    protected $parent;

    /**
     * @var array
     */
    protected $children;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $ttl;

    public function __construct()
    {
        $this->settings = [];
        $this->enabled = false;
        $this->children = [];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%s ~ #%s', $this->getName(), $this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setSettings(array $settings = []): void
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * {@inheritdoc}
     */
    public function setSetting($name, $value): void
    {
        $this->settings[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetting($name, $default = null)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function addChildren(BlockInterface $child): void
    {
        $this->children[] = $child;

        $child->setParent($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(BlockInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return $this->getParent() instanceof self;
    }

    /**
     * {@inheritdoc}
     */
    public function getTtl()
    {
        if (!$this->getSetting('use_cache', true)) {
            return 0;
        }

        $ttl = $this->getSetting('ttl', 86400);

        foreach ($this->getChildren() as $block) {
            $blockTtl = $block->getTtl();

            $ttl = ($blockTtl < $ttl) ? $blockTtl : $ttl;
        }

        $this->ttl = $ttl;

        return $this->ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }
}