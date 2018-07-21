<?php

class Notification {
    private $id;
    private $user;
    private $url;
    private $message;
    private $icon;
    private $time;
    private $viewed;
    
    public function __construct() {}

    public static function withId( $id ) {
        $notification = new self();
        $notification->getById( $id );
        return $notification;
    }

    public static function withRow( array $row ) {
        $notification = new self();
        $notification->fill( $row );
        return $notification;
    }

    protected function getById( $id ) {
        global $db;

        $result = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE id = '.$id) or error('Unable to fetch notification item', __FILE__, __LINE__, $db->error());
        $row = $db->fetch_assoc($result);

        $notification->fill( $row );
    }

    protected function fill( array $row ) {
        $this->setId( $row['id'] );
        $this->setUser( $row['user'] );
        $this->setUrl( $row['url'] );
        $this->setMessage( $row['message'] );
        $this->setIcon( $row['icon'] );
        $this->setTime( $row['time'] );
        $this->setViewed( $row['viewed'] );
    }

    // Setters
    public function setId( $id ) {
        $this->id = $id;
        return $this;
    }
    
    public function setUser( $user ) {
        $this->user = $user;
        return $this;
    }
    
    public function setUrl( $url ) {
        $this->url = $url;
        return $this;
    }

    public function setMessage( $message ) {
        $this->message = $message;
        return $this;
    }

    public function setIcon( $icon ) {
        $this->icon = $icon;
        return $this;
    }

    public function setTime( $time ) {
        $this->time = $time;
        return $this;
    }
    
    public function setViewed( $viewed ) {
        $this->viewed = $viewed;
        return $this;
    }

    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function getUrl() {
        return $this->url;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function getTime() {
        return $this->time;
    }
    
    public function getViewed() {
        return $this->viewed;
    }
}