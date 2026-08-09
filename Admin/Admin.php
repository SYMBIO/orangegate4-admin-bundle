<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Admin;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;

/**
 * OrangeGate base admin — Sonata Admin 4 port of the legacy Admin subclass.
 */
abstract class Admin extends AbstractAdmin
{
    public function toString(object $object): string
    {
        $modelManager = $this->getModelManager();
        if ($modelManager instanceof ModelManager) {
            $em = $modelManager->getEntityManager($object);
            if ($em instanceof EntityManagerInterface
                && UnitOfWork::STATE_NEW === $em->getUnitOfWork()->getEntityState($object)
            ) {
                return $this->getTranslator()->trans('new', [], 'SymbioOrangeGateAdminBundle');
            }
        }

        if (method_exists($object, '__toString') && null !== $object->__toString()) {
            return (string) $object;
        }

        return sprintf('%s:%s', ClassUtils::getClass($object), spl_object_hash($object));
    }
}
