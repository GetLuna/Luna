<?php

class Menu {
    private $items = array();

    function __construct( $show_invisible = false ) {
        $this->setItems( $show_invisible );
    }

    // Setters
    public function setItems( $show_invisible ) {
        global $db;

        $result = $db->query('SELECT * FROM '.$db->prefix.'menu'.($show_invisible ? '' : ' WHERE visible = 1').' ORDER BY disp_position') or error('Unable to fetch menu items', __FILE__, __LINE__, $db->error());

        if ($db->num_rows($result) > 0) {
            while ($cur_item = $db->fetch_assoc($result)) {
                $this->items[] = new MenuItem($cur_item['id'], $cur_item['url'], $cur_item['name'], $cur_item['disp_position'], $cur_item['visible'], $cur_item['system']);
            }
        }
    }

    // Getters
    public function getItems( $visible = true ) {
        return $this->items;
    }
}

class MenuItem {
    private $id;
    private $url;
    private $name;
    private $position;
    private $visible;
    private $system;

    function __construct( $id, $url, $name, $position, $visible, $system ) {
        $this->setId( $id );
        $this->setUrl( $url );
        $this->setName( $name );
        $this->setPosition( $position );
        $this->setVisible( $visible );
        $this->setSystem( $system );
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

    public function setName( $name ) {
        $this->name = $name;
    }

    public function setPosition( $position ) {
        $this->position = $position;
    }

    public function setVisible( $visible ) {
        $this->visible = $visible;
    }
    
    public function setSystem( $system ) {
        $this->system = $system;
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

    public function getName() {
        return $this->name;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getVisible() {
        return $this->visible;
    }
    
    public function getSystem() {
        return $this->system;
    }
}