<?php

class Page {
    private $name;
    private $url;
    private $state;
    private $rel;

    function __construct( $name, $url = null, $state = null, $rel = null ) {
        $this->setName( $name );
        $this->setUrl( $url );
        $this->setState( $state );
        $this->setRel( $rel );
    }

    // Setters
    public function setName( $name ) {
        $this->name = $name;
    }
    
    public function setUrl( $url ) {
        $this->url = $url;
    }

    public function setState( $state ) {
        $this->state = $state;
    }

    public function setRel( $rel ) {
        $this->rel = $rel;
    }

    // Getters
    public function getName() {
        return $this->name;
    }
    
    public function getUrl() {
        return $this->url;
    }

    public function getState() {
        return $this->state;
    }

    public function getRel() {
        return $this->rel;
    }
}