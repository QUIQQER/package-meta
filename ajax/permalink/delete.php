<?php

/**
 * Delete the permalink
 *
 * @param String $project
 * @param String $lang
 * @param Integer $id
 */

function package_quiqqer_meta_ajax_permalink_delete($project, $lang, $id)
{
    $Project = \QUI::getProject( $project, $lang );
    $Site    = $Project->get( $id );

    \QUI\Meta\Permalink::deletePermalinkForSite( $Site );

    try
    {
        return \QUI\Meta\Permalink::getPermalinkFor( $Site );

    } catch ( \QUI\Exception $Exception )
    {
        return '';
    }
}

\QUI::$Ajax->register(
    'package_quiqqer_meta_ajax_permalink_delete',
    array( 'project', 'lang', 'id' )
);
