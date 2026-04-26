<?php

class User {
    private $username;
    private $email;
    private $password;
    private $role;

    public function __construct($username, $email, $password, $role = "user") {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRole() {
        return $this->role;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function isAdmin() {
        return $this->role === "admin";
    }

    public function getUserInfo() {
        return $this->username . " - " . $this->email . " - " . $this->role;
    }
}

?>