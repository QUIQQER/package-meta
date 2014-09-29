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

        $title       = $Site->getAttribute( 'quiqqer.meta.site.title' );
        $robots      = $Site->getAttribute( 'quiqqer.meta.site.robots' );
        $keywords    = $Site->getAttribute( 'quiqqer.meta.site.keywords' );
        $description = $Site->getAttribute( 'quiqqer.meta.site.description' );

        if ( !$description )  {
            $description = $Project->getConfig( 'description' );
        }

        if ( !$description )  {
            $description = $Site->getAttribute( 'short' );
        }


        if ( !$keywords )  {
            $keywords = $Project->getConfig( 'keywords' );
        }

        if ( !$robots ) {
            $robots = $Project->getConfig( 'robots' );
        }

        if ( !$title ) {
            $title = $Site->getAttribute( 'title' );
        }


        $Site->setAttribute( 'meta.seotitle', $title );
        $Site->setAttribute( 'meta.robots', $robots );
        $Site->setAttribute( 'meta.description', $description );
        $Site->setAttribute( 'meta.keywords', $keywords );
    }
}
