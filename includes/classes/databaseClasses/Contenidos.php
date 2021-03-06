<?php
namespace es\ucm\fdi\aw\classes\databaseClasses;
use es\ucm\fdi\aw\classes\abstractClasses\Crud as Crud;

class Contenidos extends Crud {
    private $properties = [
        "idSong"     => "NOT NULL",
        "idPlaylist" => "NOT NULL"
    ];
    public function __construct(){
        parent::__construct("contiene",$this->properties);
    }
}