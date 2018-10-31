<?php

class Thread {
    private $id;
    private $commenter;
    private $subject;
    private $commented;
    private $first_comment_id;
    private $last_comment;
    private $last_comment_id;
    private $last_commenter;
    private $last_commenter_id;
    private $num_views;
    private $closed;
    private $pinned;
    private $important;
    private $moved_to;
    private $forum_id;
    private $soft;
    private $solved;
    private $comments = array();
    
    public function __construct() {}

    public static function withId( $id ) {
        $thread = new self();
        $thread->getById( $id );
        return $thread;
    }

    public static function withRow( array $row ) {
        $thread = new self();
        $thread->fill( $row );
        return $thread;
    }

    protected function getById( $id ) {
        global $db;

        $result = $db->query( 'SELECT * FROM '.$db->prefix.'threads WHERE id = '.$id ) or error( 'Unable to fetch thread item', __FILE__, __LINE__, $db->error() );
        $row = $db->fetch_assoc( $result );

        $thread->fill( $row  );
    }

    protected function fill( array $row ) {
        $this->setId( $row['id'] );
        $this->setCommenter( $row['commenter'] );
        $this->setSubject( $row['subject'] );
        $this->setFirstComment( $row['first_comment'] );
        $this->setFirstCommentId( $row['first_comment_id'] );
        $this->setLastComment( $row['last_comment'] );
        $this->setLastCommentId( $row['last_comment_id'] );
        $this->setLastCommenter( $row['last_commenter'] );
        $this->setLastCommenterId( $row['last_commenter_id'] );
        $this->setNumViews( $row['num_views'] );
        $this->setNumReplies( $row['num_replies'] );
        $this->setClosed( $row['closed'] );
        $this->setPinned( $row['pinned'] );
        $this->setImportant( $row['important'] );
        $this->setMovedTo( $row['moved_to'] );
        $this->setForumId( $row['forum_id'] );
        $this->setSoft( $row['soft'] );
        $this->setSolved( $row['solved'] );
        $this->setComments();
    }

    // Setters
    public function setId( $id ) {
        $this->id = $id;
        return $this;
    }

    public function setCommenter( $commenter ) {
        $this->commenter = $commenter;
        return $this;
    }

    public function setSubject( $subject ) {
        $this->subject = $subject;
        return $this;
    }

    public function setFirstComment( $first_comment ) {
        $this->first_comment = $first_comment;
        return $this;
    }

    public function setFirstCommentId( $first_comment_id ) {
        $this->first_comment_id = $first_comment_id;
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

    public function setLastCommenter( $last_commenter ) {
        $this->last_commenter = $last_commenter;
        return $this;
    }

    public function setLastCommenterId( $last_commenter_id ) {
        $this->last_commenter_id = $last_commenter_id;
        return $this;
    }

    public function setNumViews( $num_views ) {
        $this->num_views = $num_views;
        return $this;
    }

    public function setNumReplies( $num_replies ) {
        $this->num_replies = $num_replies;
        return $this;
    }

    public function setClosed( $closed ) {
        $this->closed = $closed;
        return $this;
    }

    public function setPinned( $pinned ) {
        $this->pinned = $pinned;
        return $this;
    }

    public function setImportant( $important ) {
        $this->important = $important;
        return $this;
    }

    public function setMovedTo( $moved_to ) {
        $this->moved_to = $moved_to;
        return $this;
    }

    public function setForumId( $forum_id ) {
        $this->forum_id = $forum_id;
        return $this;
    }

    public function setSoft( $soft ) {
        $this->soft = $soft;
        return $this;
    }

    public function setSolved( $solved ) {
        $this->solved = $solved;
        return $this;
    }

    public function setComments() {
        global $db;

        $comments = array();

        $result = $db->query( 'SELECT * FROM '.$db->prefix.'comments WHERE thread_id = '.$this->id ) or error( 'Unable to fetch comments', __FILE__, __LINE__, $db->error() );
        $row = $db->fetch_assoc( $result );

        while ( $row = $db->fetch_assoc( $result ) ) {
            $comments[] = Forum::withRow( $row );
        }

        $this->comments = $comments;

        return $this;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getCommenter() {
        return $this->commenter;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getFirstComment() {
        return $this->first_comment;
    }

    public function getFirstCommentId() {
        return $this->first_comment_id;
    }

    public function getLastComment() {
        return $this->last_comment;
    }

    public function getLastCommentId() {
        return $this->last_comment_id;
    }

    public function getLastCommenter() {
        return $this->last_commenter;
    }

    public function getLastCommenterId() {
        return $this->last_commenter_id;
    }

    public function getNumViews() {
        return $this->num_views;
    }

    public function getNumReplies() {
        return $this->num_replies;
    }

    public function getClosed() {
        return $this->closed;
    }

    public function getPinned() {
        return $this->pinned;
    }

    public function getImportant() {
        return $this->important;
    }

    public function getMovedTo() {
        return $this->moved_to;
    }

    public function getForumId() {
        return $this->forum_id;
    }

    public function getSoft() {
        return $this->soft;
    }

    public function getSolved() {
        return $this->solved;
    }

    public function getComments() {
        return $this->comments;
    }
}