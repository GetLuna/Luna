<?php

class Footer {
    private $threads;
    private $comments;
    private $users;
    private $latest_user;
    private $guests_online;
    private $users_online;
    private $online = array();

    function __construct() {
        global $db;

        if ( !defined( 'LUNA_CACHE_DIR' ) ) {
            define( 'LUNA_CACHE_DIR', LUNA_ROOT.'cache/' );
        }
        
        if ( file_exists( LUNA_CACHE_DIR.'cache_users_info.php' ) ) {
            include LUNA_CACHE_DIR.'cache_users_info.php';
        }
        
        if ( !defined( 'LUNA_USERS_INFO_LOADED' ) ) {
            if ( !defined( 'LUNA_CACHE_FUNCTIONS_LOADED' ) ) {
                require LUNA_ROOT.'include/cache.php';
            }
        
            generate_users_info_cache();
            require LUNA_CACHE_DIR.'cache_users_info.php';
        }
        
        // Collect some statistics from the database
        $result = $db->query( 'SELECT SUM( num_threads ), SUM( num_comments ) FROM '.$db->prefix.'forums' ) or error( 'Unable to fetch thread/comment count', __FILE__, __LINE__, $db->error() );

        list( $total_threads, $total_comments ) = array_map( 'intval', $db->fetch_row( $result ) );

        $this->setThreads( $total_threads );
        $this->setComments( $total_comments );
        $this->setUsers( $stats['total_users'] );
        $this->setLatestUser( $stats['last_user'] );
        $this->setGuestsOnline();
        $this->setUsersOnline();
        $this->setOnline();
    }

    // Setters
    public function setThreads( $threads ) {
        $this->threads = $threads;
        return $this;
    }

    public function setComments( $comments ) {
        $this->comments = $comments;
        return $this;
    }

    public function setUsers( $users ) {
        $this->users = $users;
        return $this;
    }

    public function setLatestUser( $latest_user ) {
        $this->latest_user = $latest_user;
        return $this;
    }

    public function setGuestsOnline() {
        global $db;
    
        $result_num_guests = $db->query( 'SELECT user_id FROM '.$db->prefix.'online WHERE idle=0 AND user_id=1', true ) or error( 'Unable to fetch online guests list', __FILE__, __LINE__, $db->error() );

        $this->guests_online = $db->num_rows( $result_num_guests );
    
        return $this;
    }

    public function setUsersOnline() {
        global $db;
    
        $result_num_users = $db->query( 'SELECT user_id FROM '.$db->prefix.'online WHERE idle=0 AND user_id>1', false ) or error( 'Unable to fetch online users list', __FILE__, __LINE__, $db->error() );

        $this->users_online = $db->num_rows( $result_num_users );
    
        return $this;
    }

    public function setOnline() {
        global $luna_config, $db, $luna_user;
    
        if ( $luna_config['o_users_online'] == '1' ) {
            $result = $db->query( 'SELECT user_id, ident FROM '.$db->prefix.'online WHERE idle = 0 AND user_id > 1 ORDER BY ident', true ) or error( 'Unable to fetch online list', __FILE__, __LINE__, $db->error(  ) );
    
            if ( $db->num_rows( $result ) > 0 ) {
                while ( $luna_user_online = $db->fetch_assoc( $result ) ) {
                    if ( $luna_user['g_view_users'] == '1' ) {
                        $this->online[] = array( 'url' => 'profile.php?id='.$luna_user_online['user_id'], 'name' => luna_htmlspecialchars( $luna_user_online['ident'] ) );
                    } else {
                        $this->online[] = array( 'name' => luna_htmlspecialchars( $luna_user_online['ident'] ) );
                    }
                }
            }
        }

        return $this;
    }

    // Getters
    public function getThreads( $raw = true ) {
        if ( $raw === true ) {
            return forum_number_format( $this->threads );
        } else {
            return $this->threads;
        }
    }

    public function getComments( $raw = true ) {
        if ( $raw === true ) {
            return forum_number_format( $this->comments );
        } else {
            return $this->comments;
        }
    }

    public function getUsers( $raw = true ) {
        if ( $raw === true ) {
            return forum_number_format( $this->users );
        } else {
            return $this->users;
        }
    }

    public function getLatestUser() {
        return $this->latest_user;
    }

    public function getGuestsOnline( $raw = true ) {
        if ( $raw === true ) {
            return forum_number_format( $this->guests_online );
        } else {
            return $this->guests_online;
        }
    }

    public function getUsersOnline( $raw = true ) {
        if ( $raw === true ) {
            return forum_number_format( $this->users_online );
        } else {
            return $this->users_online;
        }
    }

    public function getOnline() {
        return $this->online;
    }
}