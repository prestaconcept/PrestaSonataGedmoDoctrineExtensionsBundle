<?php
/**
 * This file is part of the PrestaSonataGedmoDoctrineExtensionsBundle
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataGedmoDoctrineExtensionsBundle\Admin;

use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;
use Presta\SonataGedmoDoctrineExtensionsBundle\AbstractTranslatable;

/**
 * This field allow you to add several translation of the same field in a listing
 *
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class TranslatedFieldDescription extends FieldDescription
{
    /**
     * Override to allow use fake field name : 'fieldName#locale' so you don't have to
     * write a getter per locale
     *
     * @param  AbstractTranslatable $object
     * @return string
     */
    public function getValue($object)
    {
        if ($object instanceof AbstractTranslatable) {
            list($field, $locale) = explode('#', $this->fieldName);
            return $object->getTranslation($field, $locale);
        }

        return parent::getValue($object);
    }
}
