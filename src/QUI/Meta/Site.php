<?php

/**
 * This file contains \QUI\Meta\Site
 */

namespace QUI\Meta;

use QUI;

/**
 * Meta Site class
 * Set the metadata for \QUI\Projects\Site Objects
 *
 * @author www.pcsg.de (Henning Leutz)
 */
class Site
{
    /**
     * event on site init
     *
     * @param QUI\Interfaces\Projects\Site $Site
     */
    public static function onInit(QUI\Interfaces\Projects\Site $Site): void
    {
        $Project = $Site->getProject();
        $lang = $Project->getLang();

        $title = $Site->getAttribute('quiqqer.meta.site.title');
        $robots = $Site->getAttribute('quiqqer.meta.site.robots');
        $description = $Site->getAttribute('quiqqer.meta.site.description');
        $canonical = $Site->getAttribute('quiqqer.meta.site.canonical');

        // meta description
        if (!$description) {
            $description = $Site->getAttribute('short');
        }

        if (empty($description)) {
            $localeDescription = QUI::getLocale()->getByLang(
                $lang,
                'quiqqer/meta',
                'quiqqer.projects.description'
            );

            if (!empty($localeDescription)) {
                $description = $localeDescription;
            }
        }

        if (!$description) {
            $description = '';
        }

        if (!$title) {
            $title = $Site->getAttribute('title');
        }

        if (!$robots) {
            $robots = $Project->getConfig('meta.project.robots');
        }

        $publisher = $Project->getConfig('meta.project.publisher');
        $copyright = $Project->getConfig('meta.project.copyright');
        $revisit = $Project->getConfig('meta.project.revisit');

        if (!$revisit) {
            $revisit = '';
        }

        if (!$title) {
            $title = '';
        }

        if (!$robots) {
            $robots = 'all';
        }

        if (!$description) {
            $description = '';
        }

        if (!$copyright) {
            $copyright = '';
        }

        if (!$publisher) {
            $publisher = '';
        }

        $Site->setAttribute('meta.revisit', $revisit);
        $Site->setAttribute('meta.itemscope', 'https://schema.org/WebPage');

        $Site->setAttribute('meta.seotitle', $title);
        $Site->setAttribute('meta.robots', $robots);
        $Site->setAttribute('meta.description', $description);

        $Site->setAttribute('meta.copyright', $copyright);
        $Site->setAttribute('meta.publisher', $publisher);

        if (!empty($canonical)) {
            $Site->setAttribute('meta.canonical', $canonical);
        }
    }
}
