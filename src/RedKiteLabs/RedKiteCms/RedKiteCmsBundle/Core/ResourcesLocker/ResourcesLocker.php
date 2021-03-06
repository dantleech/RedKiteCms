<?php
/**
 * This file is part of the RedKiteCmsBunde Application and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\ResourcesLocker;

use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Factory\FactoryRepositoryInterface;
use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\ResourcesLocker\Exception\ResourceNotFreeException;
use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Exception\General\RuntimeException;

/**
 * ResourcesLocker is responsible to manage the locked resources.
 *
 * A user could not lock more that a resource a time
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 *
 * @api
 */
class ResourcesLocker
{
    /** @var \RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Repository\LockedResourceRepositoryInterface */
    private $lockedResourceRepository;
    /** @var int */
    private $expiringTime;

    /**
     * Constructor
     *
     * @param FactoryRepositoryInterface $factoryRepository
     * @param int                          $expiringTime      The time after a not updated resource is expired
     *
     * @api
     */
    public function __construct(FactoryRepositoryInterface $factoryRepository, $expiringTime = 300)
    {
        $this->factoryRepository = $factoryRepository;
        $this->lockedResourceRepository = $this->factoryRepository->createRepository('LockedResource');
        $this->expiringTime = $expiringTime;
    }

    /**
     * Locks a resource for the current user when it is free or updates the expiring
     * time when it is locked by the same user
     *
     * @param  int                      $userId
     * @param  string                   $resourceName
     * @throws ResourceNotFreeException
     * @throws \RuntimeException
     *
     * @api
     */
    public function lockResource($userId, $resourceName)
    {
        $resource = $this->lockedResourceRepository->fromResourceNameByUser($userId, $resourceName);
        if (null === $resource) {
            if ( ! $this->isResourceFree($resourceName)) {
                throw new ResourceNotFreeException('exception_resource_locked');
            }

            $resourceClass = $this->lockedResourceRepository->getRepositoryObjectClassName();
            $resource = new $resourceClass();
            $values = array(
                'ResourceName' => $resourceName,
                'UserId' => $userId,
                'CreatedAt' => time(),
                'UpdatedAt' => time(),
            );
        } else {
            $values = array(
                'UpdatedAt' => time(),
            );
        }

        $result = $this->lockedResourceRepository
                       ->setRepositoryObject($resource)
                       ->save($values);
        if (false === $result) {
            throw new RuntimeException('exception_resource_locking_error');
        }
    }

    /**
     * Unlocks the resource held by the giving user
     *
     * @param int $userId
     *
     * @api
     */
    public function unlockUserResource($userId)
    {
        return $this->lockedResourceRepository->freeUserResource($userId);
    }

    /**
     * Unlocks the given resource
     *
     * @param string $resourceName
     *
     * @api
     */
    public function unlockResource($resourceName)
    {
        return $this->lockedResourceRepository->freeLockedResource($resourceName);
    }

    /**
     * Unlocks the expired resources
     *
     * @api
     */
    public function unlockExpiredResources()
    {
        $expiredTime = time() - $this->expiringTime;

        return $this->lockedResourceRepository->removeExpiredResources($expiredTime);
    }

    /**
     * Checks whene a resource is free
     *
     * @param  string  $resourceName
     * @return boolean
     *
     * @api
     */
    protected function isResourceFree($resourceName)
    {
        return (null === $this->lockedResourceRepository->fromResourceName($resourceName)) ? true : false;
    }
}
