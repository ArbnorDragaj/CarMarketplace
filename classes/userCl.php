<?php

class User {
    private $username;
    private $password;
    private $role;

    public function __construct($username, $password, $role = "user") {
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRole() {
        return $this->role;
    }

    public function checkPassword($password) {
        return $this->password === $password;
    }

    public function isAdmin() {
        return $this->role === "admin";
    }
}

?>