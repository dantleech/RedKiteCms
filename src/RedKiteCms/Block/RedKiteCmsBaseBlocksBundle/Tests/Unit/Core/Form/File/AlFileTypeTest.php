<?php
/**
 * This file is part of the BusinessDropCapBundle and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <info@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteCms\Block\RedKiteCmsBaseBlocksBundle\Tests\Unit\Core\Form;

use RedKiteLabs\RedKiteCmsBundle\Tests\Unit\Core\Form\Base\AlBaseType;
use RedKiteCms\Block\RedKiteCmsBaseBlocksBundle\Core\Form\File\AlFileType;

/**
 * AlFileTypeTest
 *
 * @author RedKite Labs <info@redkite-labs.com>
 */
class AlFileTypeTest extends AlBaseType
{
    protected function configureFields()
    {
        return array(
            "file",
            array(
                'name' => 'description',
                'type' => 'textarea',
            ),
            array(
                'name' => 'opened',
                'type' => 'checkbox',
                'options' => array('label' => 'file_block_show_opened'),
            ),
        );
    }
    
    protected function getForm()
    {
        return new AlFileType();
    }
}