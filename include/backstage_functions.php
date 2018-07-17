<?php

function backstage_paginate($pages) {
    if (!empty($pages)) {
        foreach ($pages as $page) {
            if ($page->getState() === 'disabled')
                $class = 'btn btn-light disabled';
            elseif ($page->getState() === 'active')
                $class = 'btn btn-light btn-light-active';
            else
                $class = 'btn btn-light';

            if ($page->getName() === 'Previous')
                $name = '<i class="fa fa-fw fa-angle-left"></i>';
            elseif ($page->getName() === 'Next')
                $name = '<i class="fa fa-fw fa-angle-right"></i>';
            else
                $name = $page->getName();

            echo '<a href="'.$page->getUrl().'" '.(($page->getRel() !== null) ? 'rel="'.$page->getRel().'"' : '').' class="'.$class.'"><span class="fw">'.$name.'</span></a>';
        }
    }
}