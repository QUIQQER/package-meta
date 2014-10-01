<?php

/**
 * This file contains \QUI\Meta\Permalink
 */

namespace QUI\Meta;

/**
 * Permalink class
 *
 * @author www.pcsg.de (Henning Leutz)
 */

class Permalink
{
    /**
     * Set the permalink for a Site
     *
     * @param \QUI\Projects\Site $Site
     * @param String $permalink
     */
    static function setPermalinkForSite($Site, $permalink)
    {
        $Project = $Site->getproject();
        $table   = \QUI::getDBProjectTableName( 'meta_permalink', $Project, false );

        $hasPermalink    = false;
        $permalinkExists = false;

        // has the site a permalink?
        try
        {
            self::getPermalinkFor( $Site );

            $hasPermalink = true;

        } catch ( \QUI\Exception $Exception )
        {

        }

        if ( $hasPermalink )
        {
            throw new \QUI\Exception(
                \QUI::getLocale()->get(
                    'quiqqer/meta',
                    'exception.permalink.couldNotSet.site.has.permalink'
                ),
                409
            );
        }

        // exist the permalink?
        try
        {
            self::getSiteByPermalink( $Project, $permalink );

            $permalinkExists = true;

        } catch ( \QUI\Exception $Exception  )
        {
            // not exist, all is ok
        }

        if ( $permalinkExists )
        {
            throw new \QUI\Exception(
                \QUI::getLocale()->get(
                    'quiqqer/meta',
                    'exception.permalink.couldNotSet.already.exists'
                ),
                409
            );
        }


        // @todo permalink prüfen ob dieser verwendet werden darf

        // clear
        $permalink = str_replace(' ', '-', $permalink);

        \QUI::getDataBase()->insert($table, array(
            'id'   => $Site->getId(),
            'lang' => $Project->getLang(),
            'link' => $permalink
        ));

        return true;
    }

    /**
     * Return the permalink from a Site
     *
     * @param \QUI\Projects\Site $Site
     * @throws \QUI\Exception
     * @return String
     */
    static function getPermalinkFor($Site)
    {
        $Project = $Site->getProject();
        $table   = \QUI::getDBProjectTableName( 'meta_permalink', $Project, false );

        $result = \QUI::getDataBase()->fetch(array(
            'from'  => $table,
            'where' => array(
                'id'   => $Site->getId(),
                'lang' => $Project->getLang()
            ),
            'limit' => 1
        ));

        if ( !isset( $result[0] ) )
        {
            throw new \QUI\Exception(
                \QUI::getLocale()->get(
                    'quiqqer/meta',
                    'exception.permalink.not.found'
                ),
                404
            );
        }

        return $result[0]['link'];
    }

    /**
     * Return the Site for a specific permalink
     *
     * @param \QUI\Projects\Project $Project
     * @param String $url
     *
     * @throws \QUI\Exception
     * @return \QUI\Projects\Site
     */
    static function getSiteByPermalink(\QUI\Projects\Project $Project, $url)
    {
        $table = \QUI::getDBProjectTableName( 'meta_permalink', $Project, false );

        $result = \QUI::getDataBase()->fetch(array(
            'from'  => $table,
            'where' => array(
                 'link' => $url
            ),
            'limit' => 1
        ));


        if ( !isset( $result[0] ) )
        {
            $params = explode( \QUI\Rewrite::URL_PARAM_SEPERATOR, $url );
            $url    = $params[0] .'.html';

            $result = \QUI::getDataBase()->fetch(array(
                'from'  => $table,
                'where' => array(
                     'link' => $url
                ),
                'limit' => 1
            ));

            if ( isset( $result[0] ) )
            {
                $_Project = \QUI::getProjectManager()->getProject(
                    $Project->getName(),
                    $result[0]['lang']
                );

                return $_Project->get( $result[0]['id'] );
            }

            throw new \QUI\Exception(
                \QUI::getLocale()->get('quiqqer/system', 'exception.site.not.found'),
                404
            );
        }

        $_Project = \QUI::getProjectManager()->getProject(
            $Project->getName(),
            $result[0]['lang']
        );

        return $_Project->get( $result[0]['id'] );
    }

    /**
     * Delete the permalink for a site
     *
     * @param \QUI\Projects\Site $Site
     * @throws \QUI\Exception
     */
    static function deletePermalinkForSite($Site)
    {
        $Project = $Site->getProject();
        $table   = \QUI::getDBProjectTableName( 'meta_permalink', $Project, false );

        \QUI::getDataBase()->delete($table, array(
            'id'   => $Site->getId(),
            'lang' => $Project->getLang()
        ));
    }

    /**
     * Events
     */

    /**
     * Event : on site save
     *
     * @param \QUI\Projects\Site\Edit $Site
     */
    static function onSave($Site)
    {
        if ( !$Site->getAttribute('quiqqer.package.meta.permalink') ) {
            return;
        }

        $permalink = $Site->getAttribute('quiqqer.package.meta.permalink');

        try
        {
            $oldLink = self::getPermalinkFor( $Site );

            if ( $oldLink == $permalink ) {
                return;
            }

        } catch ( \QUI\Exception $Exception )
        {

        }

        try
        {
            self::setPermalinkForSite( $Site, $permalink );

        } catch ( \QUI\Exception $Exception )
        {
            \QUI\System\Log::writeException( $Exception );

            \QUI::getMessagesHandler()->addError(
                \QUI::getLocale()->get(
                    'quiqqer/meta',
                    'message.permalink.could.not.set'
                )
            );
        }
    }

    /**
     * Event : on site save
     *
     * @param \QUI\Projects\Site\Edit $Site
     */
    static function onLoad($Site)
    {
        // if permalink exist, set the meta canonical
        try
        {
            $link = self::getPermalinkFor( $Site );

            if ( empty( $link ) ) {
                return;
            }

            // for the admin
            $Site->setAttribute( 'quiqqer.package.meta.permalink', $link );

            // canonical setzen
            $Site->setAttribute( 'canonical', $link );

            \QUI\System\Log::writeRecursive( $Site->getId() );
            \QUI\System\Log::writeRecursive( $link );

        } catch ( \QUI\Exception $Exception )
        {

        }
    }
}
