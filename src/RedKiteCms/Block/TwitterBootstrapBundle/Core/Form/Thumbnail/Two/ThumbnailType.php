<?php
/*
 * This file is part of the TwitterBootstrapBundle and it is distributed
 * under the MIT LICENSE. To use this application you must leave intact this copyright
 * notice.
 *
 * Copyright (c) RedKite Labs <info@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT LICENSE
 *
 */

namespace RedKiteCms\Block\TwitterBootstrapBundle\Core\Form\Thumbnail\Two;

use RedKiteCms\Block\TwitterBootstrapBundle\Core\Form\Base\BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Defines the form to edit a thumbnail block
 *
 * @author RedKite Labs <info@redkite-labs.com>
 */
class ThumbnailType extends BaseType
{
    /**
     * Builds the form
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('width', 'choice',
            array('label' => 'thumbnail_width_attribute',
                'choices' =>
                array(
                    'none' => 'none',
                    'span1' => 'span1 (60px)',
                    'span2' => 'span2 (140px)',
                    'span3' => 'span3 (220px)',
                    'span4' => 'span4 (300px)',
                    'span5' => 'span5 (380px)',
                    'span6' => 'span6 (460px)',
                    'span7' => 'span7 (540px)',
                    'span8' => 'span8 (620px)',
                    'span9' => 'span9 (700px)',
                    'span10' => 'span10 (780px)',
                    'span11' => 'span11 (860px)',
                    'span12' => 'span12 (940px)',
                )
            )
        );

        parent::buildForm($builder, $options);
    }
}
