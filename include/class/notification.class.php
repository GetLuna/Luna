<?php

class Notification {
    private $id;
    private $user;
    private $url;
    private $message;
    private $icon;
    private $time;
    private $viewed;

    function __construct( $id, $user, $url, $message, $icon, $time, $viewed ) {
        $this->setId( $id );
        $this->setUser( $user );
        $this->setUrl( $url );
        $this->setMessage( $message );
        $this->setIcon( $icon );
        $this->setTime( $time );
        $this->setViewed( $viewed );
    }

    // Setters
    public function setId( $id ) {
        $this->id = $id;
    }
    
    public function setUser( $user ) {
        $this->user = $user;
    }
    
    public function setUrl( $url ) {
        $this->url = $url;
    }

    public function setMessage( $message ) {
        $this->message = $message;
    }

    public function setIcon( $icon ) {
        $this->icon = $icon;
    }

    public function setTime( $time ) {
        $this->time = $time;
    }
    
    public function setViewed( $viewed ) {
        $this->viewed = $viewed;
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