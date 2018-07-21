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
            while ($row = $db->fetch_assoc($result)) {
                $this->items[] = MenuItem::withRow($row);
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

    public function __construct() {}

    public static function withId( $id ) {
        $menu_item = new self();
        $menu_item->getById( $id );
        return $menu_item;
    }

    public static function withRow( array $row ) {
        $menu_item = new self();
        $menu_item->fill( $row );
        return $menu_item;
    }

    protected function getById( $id ) {
        global $db;

        $result = $db->query('SELECT * FROM '.$db->prefix.'menu WHERE id = '.$id) or error('Unable to fetch menu item', __FILE__, __LINE__, $db->error());
        $row = $db->fetch_assoc($result);

        $menu_item->fill( $row );
    }

    protected function fill( array $row ) {
        $this->setId( $row['id'] );
        $this->setUrl( $row['url'] );
        $this->setName( $row['name'] );
        $this->setPosition( $row['disp_position'] );
        $this->setVisible( $row['visible'] );
        $this->setSystem( $row['sys_entry'] );
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

    public function setName( $name ) {
        $this->name = $name;
        return $this;
    }

    public function setPosition( $position ) {
        $this->position = $position;
        return $this;
    }

    public function setVisible( $visible ) {
        $this->visible = $visible;
        return $this;
    }
    
    public function setSystem( $system ) {
        $this->system = $system;
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