<?php

/**
 * This file contains \QUI\Meta\Site
 */

namespace QUI\Meta;

/**
 * Meta Site class
 * Set the meta data for \QUI\Projects\Site Objects
 *
 * @author www.pcsg.de (Henning Leutz)
 */

class Site
{
    /**
     * event on site init
     *
     * @param \QUI\Projects\Site $Site
     */
    static function onInit($Site)
    {
        $Project = $Site->getProject();

        $title = $Site->getAttribute('quiqqer.meta.site.title');
        $robots = $Site->getAttribute('quiqqer.meta.site.robots');
        $keywords = $Site->getAttribute('quiqqer.meta.site.keywords');
        $description = $Site->getAttribute('quiqqer.meta.site.description');

        $revisit = '';
        $publisher = '';
        $copyright = '';


        // meta description
        if (!$description) {
            $description = $Site->getAttribute('short');
        }

        if (!$description || empty($description)) {
            $localeDescription = \QUI::getLocale()->get(
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

        // meta kewords
        if (!$keywords) {
            $localeKeywords = \QUI::getLocale()->get(
                'quiqqer/meta',
                'quiqqer.projects.keywords'
            );

            if (!empty($localeKeywords)) {
                $keywords = $localeKeywords;
            }
        }

        // meta title
        if (!$title) {
            $title = $Site->getAttribute('title');
        }


        // settings
        if (!$robots) {
            $robots = $Project->getConfig('meta.project.robots');
        }

        if (empty($publisher)) {
            $publisher = $Project->getConfig('meta.project.publisher');
        }

        if (empty($copyright)) {
            $copyright = $Project->getConfig('meta.project.copyright');
        }

        if (empty($revisit)) {
            $revisit = $Project->getConfig('meta.project.revisit');
        }


        if (!$revisit) {
            $revisit = '';
        }

        if (!$title) {
            $title = '';
        }

        if (!$robots) {
            $robots = '';
        }

        if (!$description) {
            $description = '';
        }

        if (!$keywords) {
            $keywords = '';
        }

        if (!$copyright) {
            $copyright = '';
        }

        if (!$publisher) {
            $publisher = '';
        }


        $Site->setAttribute('meta.revisit', $revisit);

        $Site->setAttribute('meta.seotitle', $title);
        $Site->setAttribute('meta.robots', $robots);
        $Site->setAttribute('meta.description', $description);
        $Site->setAttribute('meta.keywords', $keywords);

        $Site->setAttribute('meta.copyright', $copyright);
        $Site->setAttribute('meta.publisher', $publisher);
    }
}
