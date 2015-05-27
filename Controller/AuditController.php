<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symbio\OrangeGate\AdminBundle\Controller;

use SimpleThings\EntityAudit\Controller\AuditController as BaseAuditController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AuditController extends BaseAuditController
{
	protected $admin;

	/**
     * Return to older history revision for object
     *
     * @param int|string|null $id
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     * @throws NotFoundHttpException If the object does not exist or the audit reader is not available
     *
     * @Route("/admin/history/{id}/revision/{revision}/{adminCode}", name="symbio_orangegate_admin_history_back_to_revision")
     */
    public function historyBackToRevisionAction($id = null, $revision = null, $adminCode = null)
    {
    	$em = $this->getDoctrine()->getManager();
    	$this->configureAdmin($adminCode);

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $manager = $this->get('sonata.admin.audit.manager');

        if (!$manager->hasReader($this->admin->getClass())) {
            throw new NotFoundHttpException(
                sprintf(
                    'unable to find the audit reader for class : %s',
                    $this->admin->getClass()
                )
            );
        }

        $reader = $manager->getReader($this->admin->getClass());


        // retrieve the revisioned object
        $revision = $reader->find($this->admin->getClass(), $id, $revision);

        if (!$revision) {
            throw new NotFoundHttpException(
                sprintf(
                    'unable to find the targeted object `%s` from the revision `%s` with classname : `%s`',
                    $id,
                    $revision,
                    $this->admin->getClass()
                )
            );
        }

        // set object's values to revision's values
        $regexp = "/^set[a-zA-Z]*/";
        foreach(get_class_methods(get_class($object)) as $method) {
        	if (preg_match($regexp, $method)) {
        		$getter = str_replace("set", "get", $method);
        		if (method_exists($this->admin->getClass(), $method) && method_exists($this->admin->getClass(), $getter)) {
        			try {
        				$object->$method($revision->$getter());
        			} catch(\Exception $e) {
        				continue;
        			}
        		}
        	}
        }

        $em->persist($object);
        $em->flush();

        $revisions = $reader->findRevisions($this->admin->getClass(), $id);

        // success flash message
        $this->addFlash(
            'sonata_flash_success',
            $this->admin->trans(
                'flash_revision_back',
                array(),
                'SymbioOrangeGateAdminBundle'
            )
        );

        return $this->render($this->admin->getTemplate('history'), array(
            'action'            => 'history',
            'object'            => $object,
            'revisions'         => $revisions,
            'currentRevision'   => $revisions ? current($revisions) : false,
        ));
    }


    /**
     * Configure admin class by the admin code
     *
     * @param int|string|null $id
     *
     * @throws RuntimeException If admin class doesn't exist
     */
	protected function configureAdmin($adminCode)
	{
        if (!$adminCode) {
            throw new \RuntimeException(sprintf(
                'There is no `_sonata_admin` defined for the controller `%s` and the current route `%s`',
                get_class($this),
                $this->container->get('request')->get('_route')
            ));
        }

        $this->admin = $this->container->get('sonata.admin.pool')->getAdminByAdminCode($adminCode);

        if (!$this->admin) {
            throw new \RuntimeException(sprintf(
                'Unable to find the admin class related to the current controller (%s)',
                get_class($this)
            ));
        }

        $rootAdmin = $this->admin;

        if ($this->admin->isChild()) {
            $this->admin->setCurrentChild(true);
            $rootAdmin = $rootAdmin->getParent();
        }

        $request = $this->container->get('request');

        $rootAdmin->setRequest($request);

        if ($request->get('uniqid')) {
            $this->admin->setUniqid($request->get('uniqid'));
        }
	}

	/**
     * Returns the base template name
     *
     * @return string The template name
     */
    protected function getBaseTemplate()
    {
        if ($this->get('request')->isXmlHttpRequest() || $this->get('request')->get('_xml_http_request')) {
            return $this->admin->getTemplate('ajax');
        }

        return $this->admin->getTemplate('layout');
    }

    /**
     * {@inheritdoc}
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $parameters['admin']         = isset($parameters['admin']) ?
            $parameters['admin'] :
            $this->admin;

        $parameters['base_template'] = isset($parameters['base_template']) ?
            $parameters['base_template'] :
            $this->getBaseTemplate();

        $parameters['admin_pool']    = $this->get('sonata.admin.pool');

        return parent::render($view, $parameters, $response);
    }


     /**
     * Adds a flash message for type.
     *
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $this->get('session')
             ->getFlashBag()
             ->add($type, $message);
    }
}