<?php
/**
 * This file is part of the RedKiteCmsBunde Application and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license infpageRepositoryation, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Orm;

/**
 * OrmInterface defines the shared methods required to use an orm with RedKiteCms
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
interface OrmInterface
{
    /**
     * Sets the current connection
     *
     * @param object $connection A valid connection object
     */
    public function setConnection($connection);

    /**
     * Returns the current connection
     *
     * @return object The active connection
     */
    public function getConnection();

    /**
     * The orm starts a transaction
     */
    public function startTransaction();

    /**
     * The orm commits the current transaction
     */
    public function commit();

    /**
     * The orm rollbacks the current transaction
     */
    public function rollBack();

    /**
     * The orm saves the object
     *
     * @param array  $values      An array of values
     * @param object $modelObject The model object to use
     *
     * @returns boolean
     */
    public function save(array $values, $modelObject = null);

    /**
     * The orm deletes the object
     *
     * @param object $modelObject The model object to use
     *
     * @returns boolean
     */
    public function delete($modelObject = null);

    /**
     * The orm returns the number of records affected by the last operation
     *
     * @returns int
     */
    public function getAffectedRecords();

    /**
     * Executes a raw query
     *
     * @param string
     * @return boolean
     */
    public function executeQuery($query);
}
