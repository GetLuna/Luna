<?php

class Forum {
    private $id;
    private $name;
    private $description;
    private $moderators;
    private $num_threads;
    private $num_comments;
    private $last_comment;
    private $last_comment_id;
    private $last_commenter_id;
    private $sort_by;
    private $position;
    private $cat_id;
    private $color;
    private $parent_id;
    private $solved;
    private $icon;
    private $icon_style;
    private $subforums = array();
    private $last;
    
    public function __construct() {}

    public static function withId( $id ) {
        global $db;

        $forum = new self();

        $result = $db->query( 'SELECT * FROM '.$db->prefix.'forums WHERE id = '.$id ) or error( 'Unable to fetch forum item', __FILE__, __LINE__, $db->error() );
        $row = $db->fetch_assoc( $result );

        $forum->fill( $row );

        return $forum;
    }

    public static function withRow( array $row ) {
        $forum = new self();
        $forum->fill( $row );
        return $forum;
    }

    protected function fill( array $row ) {
        $this->setId( $row['id'] );
        $this->setName( $row['forum_name'] );
        $this->setDescription( $row['forum_desc'] );
        $this->setModerators( $row['moderators'] );
        $this->setNumThreads( $row['num_threads'] );
        $this->setNumComments( $row['num_comments'] );
        $this->setLastComment( $row['last_comment'] );
        $this->setLastCommentId( $row['last_comment_id'] );
        $this->setLastCommenterId( $row['last_commenter_id'] );
        $this->setSortBy( $row['sort_by'] );
        $this->setPosition( $row['disp_position'] );
        $this->setCatId( $row['cat_id'] );
        $this->setColor( $row['color'] );
        $this->setParentId( $row['parent_id'] );
        $this->setSolved( $row['solved'] );
        $this->setIcon( $row['icon'] );
        $this->setIconStyle( $row['icon_style'] );
        $this->setSubforums();
        $this->setLast( $row['last_comment_id'] );
    }

    // Setters
    public function setId( $id ) {
        $this->id = $id;
        return $this;
    }

    public function setName( $name ) {
        $this->name = $name;
        return $this;
    }

    public function setDescription( $description ) {
        $this->description = $description;
        return $this;
    }

    public function setModerators( $moderators ) {
        $this->moderators = $moderators;
        return $this;
    }

    public function setNumThreads( $num_threads ) {
        $this->num_threads = $num_threads;
        return $this;
    }

    public function setNumComments( $num_comments ) {
        $this->num_comments = $num_comments;
        return $this;
    }

    public function setLastComment( $last_comment ) {
        $this->last_comment = $last_comment;
        return $this;
    }

    public function setLastCommentId( $last_comment_id ) {
        $this->last_comment_id = $last_comment_id;
        return $this;
    }

    public function setLastCommenterId( $last_commenter_id ) {
        $this->last_commenter_id = $last_commenter_id;
        return $this;
    }

    public function setSortBy( $sort_by ) {
        $this->sort_by = $sort_by;
        return $this;
    }

    public function setPosition( $disp_position ) {
        $this->disp_position = $disp_position;
        return $this;
    }

    public function setCatId( $cat_id ) {
        $this->cat_id = $cat_id;
        return $this;
    }

    public function setColor( $color ) {
        $this->color = $color;
        return $this;
    }

    public function setParentId( $parent_id ) {
        $this->parent_id = $parent_id;
        return $this;
    }

    public function setSolved( $solved ) {
        $this->solved = $solved;
        return $this;
    }

    public function setIcon( $icon ) {
        $this->icon = $icon;
        return $this;
    }

    public function setIconStyle( $icon_style ) {
        $this->icon_style = $icon_style;
        return $this;
    }

    public function setForums() {
        return $this;
    }

    public function setLast() {
        global $db;

        if ( $this->last_comment_id !== null ) {
            $result = $db->query( 'SELECT * FROM '.$db->prefix.'comments WHERE id = '.$this->last_comment_id ) or error( 'Unable to fetch comments item', __FILE__, __LINE__, $db->error() );

            return $db->fetch_assoc( $result );
        }

        return null;
    }

    public function setSubforums() {
        global $db;

        $subforums = array();

        $result = $db->query( 'SELECT * FROM '.$db->prefix.'forums WHERE parent_id = '.$this->id ) or error( 'Unable to fetch subforums', __FILE__, __LINE__, $db->error() );
        $row = $db->fetch_assoc( $result );

        while ( $row = $db->fetch_assoc( $result ) ) {
            $subforums[] = Forum::withRow( $row );
        }

        $this->subforums = $subforums;

        return $this;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return luna_htmlspecialchars( $this->name );
    }

    public function getDescription() {
        return luna_htmlspecialchars( $this->description );
    }

    public function getModerators() {
        return $this->moderators;
    }

    public function getNumThreads() {
        return $this->num_threads;
    }

    public function getNumComments() {
        return $this->num_comments;
    }

    public function getLastComment() {
        return $this->last_comment;
    }

    public function getLastCommentId() {
        return $this->last_comment_id;
    }

    public function getLastCommenterId() {
        return $this->last_commenter_id;
    }

    public function getSortBy() {
        return $this->sort_by;
    }

    public function getPosition() {
        return $this->disp_position;
    }

    public function getCatId() {
        return $this->cat_id;
    }

    public function getColor() {
        return $this->color;
    }

    public function getParentId() {
        return $this->parent_id;
    }

    public function getSolved() {
        return $this->solved;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function getIconStyle() {
        return $this->icon_style;
    }

    public function getLast() {
        return $this->last_comment_id;
    }

    public function getSubforums() {
        return $this->subforums;
    }

    public function getIconMarkup() {
        global $luna_config;

        if ( $this->icon == null ) {
            return '';
        }

        if ( $this->icon_style == null ) {
            $this->icon_style = $luna_config['icon_style'];
        }

        switch ( $this->icon_style ) {
            case 0:
                return '<i class="fas fa-fw fa-'.$this->icon.'"></i>';
                break;
            case 1:
                return '<i class="far fa-fw fa-'.$this->icon.'"></i>';
                break;
            case 2:
                return '<i class="fal fa-fw fa-'.$this->icon.'"></i>';
                break;
            case 3:
                return '<i class="fab fa-fw fa-'.$this->icon.'"></i>';
                break;
        }
    }

    public function getForumUrl() {
        return "viewforum.php?id=".$this->id;
    }
    
    public function hasNewThreads() {
        global $new_threads;

        return isset( $new_threads[$this->id] );
    }
    
    public function isActive() {
        global $forum_id;

        return $forum_id == $this->id;
    }

    public function getForumClasses() {
        $classes = array();

        if ( $this->hasNewThreads() ) {
            $classes[] = 'new-item';
        }

        if ( $this->isActive() ) {
            $classes[] = 'active';
        }

        return implode( ' ', $classes );
    }
}