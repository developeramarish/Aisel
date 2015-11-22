<?php

/*
 * This file is part of the Aisel package.
 *
 * (c) Ivan Proskuryakov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aisel\ResourceBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Aisel\ResourceBundle\Entity\NodeInterface;
use Aisel\ResourceBundle\Entity\Node;

/**
 * Class NodePersistenceListener
 *
 * @author Ivan Proskuryakov <volgodark@gmail.com>
 */
class NodePersistenceListener
{

    /**
     * postUpdate
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifeCycleEventArgs $args)
    {
        /** @var Node $object */
        /** @var Node $parent */
        /** @var Node $child */

        $dm = $args->getEntityManager();
        $object = $args->getEntity();

        if ($object instanceof NodeInterface) {

            if ($parent = $object->getParent()) {
                foreach ($parent->getChildren() as $child) {

                    if ($child->getId() == $object->getId()) {
                        $parent->removeChild($child);
                    }
                }
                $parent->addChild($object);

                $dm->persist($parent);
                $dm->flush();
            }
        }
    }

    /**
     * postPersist
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifeCycleEventArgs $args)
    {
        /** @var Node $parent */
        /** @var Node $object */

        $dm = $args->getEntityManager();
        $object = $args->getEntity();

        if ($object instanceof NodeInterface) {

            if ($parent = $object->getParent()) {
                $parent->addChild($object);
                $dm->persist($parent);
                $dm->flush();
            }
        }
    }

}
