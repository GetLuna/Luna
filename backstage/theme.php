<?php

/*
 * Copyright ( C ) 2013-2018 Luna
 * Based on code by FluxBB copyright ( C ) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright ( C ) 2002-2008 PunBB
 * Licensed under GPLv2 ( http://getluna.org/license.php )
 */

define( 'LUNA_ROOT', '../' );
require LUNA_ROOT.'include/common.php';
define( 'LUNA_SECTION', 'settings' );
define( 'LUNA_PAGE', 'theme' );

$themes = forum_list_themes();
$installed_themes = array();
$i = 0;

$installed = $db->query( 'SELECT * FROM '.$db->prefix.'themes' ) or error( 'Unable to fetch themes', __FILE__, __LINE__, $db->error() );

while ( $cur_theme = $db->fetch_assoc( $installed ) ) {
    $installed_themes[] = $cur_theme['name'];
}

if ( isset( $_GET['set'] ) ) {
    confirm_referrer( 'backstage/theme.php', __( 'Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database ( look for o_base_url in the config table ) and then clear the cache by deleting all .php files in the /cache directory.', 'luna' ) );

    $theme = forum_get_theme( $_GET['set'] );

    $db->query( 'UPDATE '.$db->prefix.'config SET conf_value = \''.$theme->name.'\' WHERE conf_name=\'o_default_style\'' ) or error( 'Unable to update theme config', __FILE__, __LINE__, $db->error() );

    // Regenerate the config cache
    if ( !defined( 'LUNA_CACHE_FUNCTIONS_LOADED' ) ) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_config_cache();
    clear_feed_cache();

    //redirect( 'backstage/theme.php?saved=true' );
}

if ( isset( $_GET['uninstall'] ) ) {
    confirm_referrer( 'backstage/theme.php', __( 'Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database ( look for o_base_url in the config table ) and then clear the cache by deleting all .php files in the /cache directory.', 'luna' ) );

    $theme = forum_get_theme( $_GET['uninstall'] );

    if ( $luna_config['o_default_style'] != $theme->name ) {
        $db->query( 'DELETE FROM '.$db->prefix.'themes WHERE name = \''.$theme->id.'\'' ) or error( 'Unable to uninstall theme', __FILE__, __LINE__, $db->error() );

        if ( isset( $theme->config ) && count( $theme->config ) >= 1 ) {
            foreach ( $theme->config as $config ) {
                build_config( 0, 't_'.$theme->id.'_'.$config->name );
            }
        }
    }

    // Regenerate the config cache
    if ( !defined( 'LUNA_CACHE_FUNCTIONS_LOADED' ) ) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_config_cache();
    clear_feed_cache();

    redirect( 'backstage/theme.php?saved=true' );
}

if ( isset( $_GET['install'] ) ) {
    confirm_referrer( 'backstage/theme.php', __( 'Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database ( look for tbase_url in the config table ) and then clear the cache by deleting all .php files in the /cache directory.', 'luna' ) );

    $theme = forum_get_theme( $_GET['install'] );

    if ( !in_array( $_GET['delete'], $installed_themes ) ) {
        $db->query( 'INSERT INTO '.$db->prefix.'themes ( version, name ) VALUES( \''.$theme->version.'\', \''.$db->escape( $theme->id ).'\' )' ) or error( 'Unable to install theme', __FILE__, __LINE__, $db->error() );

        if ( isset( $theme->config ) && count( $theme->config ) >= 1 ) {
            foreach ( $theme->config as $config ) {
                build_config( 1, 't_'.$theme->id.'_'.$config->name, $config->default );
            }
        }
    }

    // Regenerate the config cache
    if ( !defined( 'LUNA_CACHE_FUNCTIONS_LOADED' ) ) {
        require LUNA_ROOT.'include/cache.php';
    }

    generate_config_cache();
    clear_feed_cache();

    redirect( 'backstage/theme.php?saved=true' );
}

if ( isset( $_GET['delete'] ) ) {
    confirm_referrer( 'backstage/theme.php', __( 'Bad HTTP_REFERER. If you have moved these forums from one location to another or switched domains, you need to update the Base URL manually in the database ( look for o_base_url in the config table ) and then clear the cache by deleting all .php files in the /cache directory.', 'luna' ) );

    if ( $luna_config['o_default_style'] != $theme->name && !in_array( $_GET['delete'], $installed_themes ) ) {
        $dir = LUNA_ROOT.'/themes/'.$_GET['delete'];
        $it = new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS );
        $files = new RecursiveIteratorIterator( $it,
                    RecursiveIteratorIterator::CHILD_FIRST );
        foreach( $files as $file ) {
            if ( $file->isDir() ){
                rmdir( $file->getRealPath() );
            } else {
                unlink( $file->getRealPath() );
            }
        }
        rmdir( $dir );
    }

    redirect( 'backstage/theme.php?saved=true' );
}

require 'header.php';
?>
<div class="row">
	<div class="col-md-12">
<?php
if ( isset( $_GET['saved'] ) ) {
    echo '<div class="alert alert-success"><i class="fas fa-fw fa-check"></i> '.__( 'Your settings have been saved.', 'luna' ).'</div>';
}
?>
        <div class="card">
            <h5 class="card-header">
                <?php _e( 'Installed themes', 'luna' )?>
            </h5>
<?php
foreach ( $themes as $theme ) {
    if ( in_array( $theme->id, $installed_themes ) ) {
?>
            <div class="row row-extension">
                <div class="col-md-3 col-12">
                    <p class="name"><?php echo $theme->name ?></p>
                    <p class="version"><?php echo $theme->version ?></p>
                    <p class="actions">
                        <?php if ( $luna_config['o_default_style'] == $theme->name ) { ?>
                            <span class="active"><?php _e( 'Active', 'luna' )?></span>
                        <?php } else { ?>
                            <a href="theme.php?set=<?php echo $theme->id ?>"><?php _e( 'Activate', 'luna' )?></a> &middot; <a href="theme.php?uninstall=<?php echo $theme->id ?>"><?php _e( 'Uninstall', 'luna' )?></a>
                        <?php } ?>
                    </p>
                </div>
                <div class="col-md-9 col-12">
                    <p class="description"><?php echo $theme->description ?></p>
                    <?php if ( Version::LUNA_VERSION < $theme->minversion ) { ?>
                        <p class="alert alert-danger">
                            <?php echo sprintf( __( 'This theme does not support your current version of Luna, please update to Luna %s.', 'luna' ), $theme->minversion ) ?>
                        </p>
                    <?php } if ( Version::LUNA_VERSION > $theme->maxversion ) { ?>
                        <p class="alert alert-warning">
                            <?php echo sprintf( __( 'This theme is not reported to support Luna %s, you might want to update it.', 'luna' ), Version::LUNA_VERSION ) ?>
                        </p>
                    <?php } if ( isset( $theme->parent ) ) { ?>
                        <p class="parent">
                            <?php echo sprintf( __( 'This theme requires a theme with the id <b>%s</b>.', 'luna' ), $theme->parent ) ?>
                        </p>
                    <?php } ?>
                    <p class="meta"><?php _e( 'Version', 'luna' )?> <?php echo $theme->version ?> &middot; <?php _e( 'By', 'luna' )?> <a href="<?php echo $theme->url ?>"><?php echo $theme->developer ?></a></p>
                </div>
            </div>
<?php
        unset( $themes[$i] );
    }
    $i++;
}
?>
        </div>
        <div class="card">
            <h5 class="card-header">
                <?php _e( 'Available themes', 'luna' )?>
            </h5>
<?php 
if ( count( $themes ) > 0 ) {
    foreach ( $themes as $theme ) { ?>
            <div class="row row-extension">
                <div class="col-md-3 col-12">
                    <p class="name"><?php echo $theme->name ?></p>
                    <p class="version"><?php echo $theme->version ?></p>
                    <p class="actions">
                        <?php if ( $luna_config['o_default_style'] == $theme->name ) { ?>
                            <span class="active"><?php _e( 'Active', 'luna' )?></span>
                        <?php } else { ?>
                            <a href="theme.php?install=<?php echo $theme->name ?>"><?php _e( 'Install', 'luna' )?></a> &middot;
                            <a href="theme.php?delete=<?php echo $theme->name ?>" class="text-danger"><?php _e( 'Delete', 'luna' )?></a>
                        <?php } ?>
                    </p>
                </div>
                <div class="col-md-9 col-12">
                    <p class="description"><?php echo $theme->description ?></p>
                    <?php if ( Version::LUNA_VERSION < $theme->minversion ) { ?>
                        <p class="alert alert-danger">
                            <?php echo sprintf( __( 'This theme does not support your current version of Luna, please update to Luna %s.', 'luna' ), $theme->minversion ) ?>
                        </p>
                    <?php } if ( Version::LUNA_VERSION > $theme->maxversion ) { ?>
                        <p class="alert alert-warning">
                            <?php echo sprintf( __( 'This theme is not reported to support Luna %s, you might want to update it.', 'luna' ), Version::LUNA_VERSION ) ?>
                        </p>
                    <?php } ?>
                    <p class="meta"><?php _e( 'Version', 'luna' )?> <?php echo $theme->version ?> &middot; <?php _e( 'By', 'luna' )?> <a href="<?php echo $theme->url ?>"><?php echo $theme->developer ?></a></p>
                </div>
            </div>
<?php
    }
} else {
?>
            <div class="card-body">
                <h3 class="text-center"><?php _e( 'There are no themes available', 'luna' )?></h3>
            </div>
<?php } ?>
        </div>
    </div>
</div>
<?php

require 'footer.php';
