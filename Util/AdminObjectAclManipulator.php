<?php

namespace Symbio\OrangeGate\AdminBundle\Util;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Sonata\AdminBundle\Util\AdminObjectAclData;
use Sonata\AdminBundle\Util\AdminObjectAclManipulator as BaseAdminObjectAclManipulator;

class AdminObjectAclManipulator extends BaseAdminObjectAclManipulator
{
    public function createForm(AdminObjectAclData $data)
    {
        // Retrieve object identity
        $objectIdentity = ObjectIdentity::fromDomainObject($data->getObject());
        $acl = $data->getSecurityHandler()->getObjectAcl($objectIdentity);
        if (!$acl) {
            $acl = $data->getSecurityHandler()->createAcl($objectIdentity);
        }

        $data->setAcl($acl);

        $masks = $data->getMasks();

        // Create a form to set ACL
        $formBuilder = $this->formFactory->createBuilder('form');
        foreach ($data->getAclUsers() as $aclUser) {
            $securityIdentity = UserSecurityIdentity::fromAccount($aclUser);

            foreach ($data->getUserPermissions() as $permission) {
                try {
                    $checked = $acl->isGranted(array($masks[$permission]), array($securityIdentity));
                } catch (NoAceFoundException $e) {
                    $checked = false;
                }

                $formBuilder->add($aclUser->getId() . $permission, 'checkbox', array('required' => false, 'data' => $checked, 'label' => ' '));
            }
        }

        $form = $formBuilder->getForm();
        $data->setForm($form);

        return $form;
    }

    /**
     * Updates ACL
     *
     * @param \Sonata\AdminBundle\Util\AdminObjectAclData $data
     */
    public function updateAcl(AdminObjectAclData $data)
    {
        foreach ($data->getAclUsers() as $aclUser) {
            $securityIdentity = UserSecurityIdentity::fromAccount($aclUser);

            $maskBuilder = new $this->maskBuilderClass();
            foreach ($data->getUserPermissions() as $permission) {
                if ($data->getForm()->get($aclUser->getId() . $permission)->getData()) {
                    $maskBuilder->add($permission);
                }
            }

            $masks = $data->getMasks();
            $acl = $data->getAcl();

            // Restore OWNER and MASTER permissions
            /*if (!$data->isOwner()) {
                foreach ($data->getOwnerPermissions() as $permission) {
                    if ($acl->isGranted(array($masks[$permission]), array($securityIdentity))) {
                        $maskBuilder->add($permission);
                    }
                }
            }*/

            $mask = $maskBuilder->get();

            $index = null;
            $ace = null;
            foreach ($acl->getObjectAces() as $currentIndex => $currentAce) {
                if ($currentAce->getSecurityIdentity()->equals($securityIdentity)) {
                    $index = $currentIndex;
                    $ace = $currentAce;
                    break;
                }
            }

            if ($ace) {
                $acl->updateObjectAce($index, $mask);
            } else {
                $acl->insertObjectAce($securityIdentity, $mask);
            }
        }

        $data->getSecurityHandler()->updateAcl($acl);
    }
}
