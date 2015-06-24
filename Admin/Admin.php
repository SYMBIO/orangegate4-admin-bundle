<?php

namespace Symbio\OrangeGate\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Doctrine\Common\Util\ClassUtils;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

class Admin extends BaseAdmin
{
    protected $listModes = array(
        'list' => array(
            'class' => 'fa fa-list fa-fw',
        ),
//        'mosaic' => array(
//            'class' => 'fa fa-th-large fa-fw',
//        ),
//        'tree' => array(
//            'class' => 'fa fa-sitemap fa-fw',
//        ),
    );

    /**
     * Generates the breadcrumbs array
     *
     * Note: the method will be called by the top admin instance (parent => child)
     *
     * @param string                       $action
     * @param \Knp\Menu\ItemInterface|null $menu
     *
     * @return array
     */
    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        if (isset($this->breadcrumbs[$action])) {
            return $this->breadcrumbs[$action];
        }

        if (!$menu) {
            $menu = $this->menuFactory->createItem('root');

            $menu = $menu->addChild(
                $this->trans($this->getLabelTranslatorStrategy()->getLabel('dashboard', 'breadcrumb', 'link'), array(), 'SymbioOrangeGateAdminBundle'),
                array(
                    'uri' => $this->routeGenerator->generate('sonata_admin_dashboard'),
                    'attributes' => array(
                        'icon' => '<i class="fa fa-dashboard"></i>'
                    )
                )
            );
        }

        $menu = $menu->addChild(
            $this->trans($this->getLabel(), array(), $this->translationDomain),
            array('uri' => $this->hasRoute('list') && $this->isGranted('LIST') ? $this->generateUrl('list') : null)
        );

        $childAdmin = $this->getCurrentChildAdmin();

        if ($childAdmin) {
            $id = $this->request->get($this->getIdParameter());

            $menu = $menu->addChild(
                $this->toString($this->getSubject()),
                array('uri' => $this->hasRoute('edit') && $this->isGranted('EDIT') ? $this->generateUrl('edit', array('id' => $id)) : null)
            );

            return $childAdmin->buildBreadcrumbs($action, $menu);

        } elseif ($this->isChild()) {

            if ($action == 'list') {
                $menu->setUri(false);
            } elseif ($action != 'create' && $this->hasSubject()) {
                $menu = $menu->addChild($this->toString($this->getSubject()));
            } else {
                $menu = $menu->addChild(
                    $this->trans($this->getLabelTranslatorStrategy()->getLabel(sprintf('%s_%s', $this->getClassnameLabel(), $action), 'breadcrumb', 'link'))
                );
            }

        } elseif ($action != 'list' && $this->hasSubject()) {
            $menu = $menu->addChild($this->toString($this->getSubject()));
        } elseif ($action != 'list') {
            $menu = $menu->addChild(
                $this->trans($this->getLabelTranslatorStrategy()->getLabel(sprintf('%s_%s', $this->getClassnameLabel(), $action), 'breadcrumb', 'link'))
            );
        }

        return $this->breadcrumbs[$action] = $menu;
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        if (!is_object($object)) {
            return '';
        }

        if (\Doctrine\ORM\UnitOfWork::STATE_NEW === $this->getModelManager()->getEntityManager(get_class($object))->getUnitOfWork()->getEntityState($object)) {
            return $this->trans('new', array(), 'SymbioOrangeGateAdminBundle');
        }

        if (method_exists($object, '__toString') && null !== $object->__toString()) {
            return (string) $object;
        }

        return sprintf("%s:%s", ClassUtils::getClass($object), spl_object_hash($object));
    }
}