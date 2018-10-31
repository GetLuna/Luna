<?php

require LUNA_ROOT.'include/class/forum.class.php';

class Category {
    private $id;
    private $name;
    private $position;
    private $forums = array();
    
    public function __construct() {}

    public static function withId( $id ) {
        $category = new self();
        $category->getById( $id );
        return $category;
    }

    public static function withRow( array $row ) {
        $category = new self();
        $category->fill( $row );
        return $category;
    }

    protected function getById( $id ) {
        global $db;

        $result = $db->query( 'SELECT * FROM '.$db->prefix.'categories WHERE id = '.$id ) or error( 'Unable to fetch category item', __FILE__, __LINE__, $db->error() );
        $row = $db->fetch_assoc( $result );

        $category->fill( $row  );
    }

    protected function fill( array $row ) {
        $this->setId( $row['id'] );
        $this->setName( $row['cat_name'] );
        $this->setPosition( $row['disp_position'] );
        $this->setForums();
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
    
    public function setPosition( $position ) {
        $this->position = $position;
        return $this;
    }

    public function setForums() {
        global $db;

        $forums = array();

        $result = $db->query( 'SELECT * FROM '.$db->prefix.'forums WHERE cat_id = '.$this->id.' and parent_id = 0' ) or error( 'Unable to fetch forums', __FILE__, __LINE__, $db->error() );

        while ( $row = $db->fetch_assoc( $result ) ) {
            $forums[] = Forum::withRow( $row );
        }

        $this->forums = $forums;

        return $this;
    }

    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return luna_htmlspecialchars( $this->name );
    }
    
    public function getPosition() {
        return $this->position;
    }

    public function getForums() {
        return $this->forums;
    }
}