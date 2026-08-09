<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Controller;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Restore an admin object to a previous audit revision.
 *
 * Port of the legacy OrangeGate historyBackToRevision action to SF 7 / Sonata 4.
 */
final class AuditController extends AbstractController
{
    public function __construct(
        private readonly Pool $adminPool,
    ) {
    }

    #[Route(
        '/admin/history/{id}/revision/{revision}/{adminCode}',
        name: 'symbio_orangegate_admin_history_back_to_revision',
        methods: ['GET', 'POST']
    )]
    public function historyBackToRevision(
        Request $request,
        string $id,
        string $revision,
        string $adminCode,
    ): Response {
        $admin = $this->adminPool->getInstance($adminCode);
        $admin->setRequest($request);

        $object = $admin->getObject($id);
        if (null === $object) {
            throw new NotFoundHttpException(sprintf('Unable to find the object with id: %s', $id));
        }

        if (false === $admin->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        if (!$this->container->has('sonata.admin.audit.manager')) {
            throw new NotFoundHttpException('Audit manager is not available.');
        }

        $manager = $this->container->get('sonata.admin.audit.manager');
        if (!$manager->hasReader($admin->getClass())) {
            throw new NotFoundHttpException(sprintf(
                'Unable to find the audit reader for class: %s',
                $admin->getClass()
            ));
        }

        $reader = $manager->getReader($admin->getClass());
        $revisionObject = $reader->find($admin->getClass(), $id, $revision);
        if (null === $revisionObject) {
            throw new NotFoundHttpException(sprintf(
                'Unable to find revision `%s` for object `%s` of class `%s`',
                $revision,
                $id,
                $admin->getClass()
            ));
        }

        foreach (get_class_methods($object) as $method) {
            if (!preg_match('/^set[A-Za-z]/', $method)) {
                continue;
            }
            $getter = 'get'.substr($method, 3);
            if (!method_exists($revisionObject, $getter)) {
                $getter = 'is'.substr($method, 3);
                if (!method_exists($revisionObject, $getter)) {
                    continue;
                }
            }
            $object->{$method}($revisionObject->{$getter}());
        }

        $admin->update($object);
        $this->addFlash('sonata_flash_success', 'flash_history_restore_success');

        return new RedirectResponse($admin->generateUrl('edit', ['id' => $id]));
    }
}
