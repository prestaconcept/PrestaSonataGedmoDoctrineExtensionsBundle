<?php
/**
 * This file is part of the PrestaSonataGedmoDoctrineExtensionsBundle
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataGedmoDoctrineExtensionsBundle\Entity;

interface TranslatableInterface
{
    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param AbstractTranslation $translation
     */
    public function addTranslation(AbstractTranslation $translation);

    /**
     * @return ArrayCollection
     */
    public function getTranslations();

    /**
     * Return field translation for a given locale
     *
     * @param  string $field
     * @param  string $locale
     * @return string
     */
    public function getTranslation($field, $locale);
}
