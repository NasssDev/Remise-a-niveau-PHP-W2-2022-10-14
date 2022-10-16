<?php

class Users
{

    private $id;
    private $username;
    private $password;
    private $role;

    private $db;
    private $name_table;
    private $errorss = [];

    public function __construct($maconnexion = "", $my_table = "users")
    {
        $this->db = $maconnexion;
        $this->name_table = $my_table;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password = null)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getErrors()
    {
        return $this->errorss;
    }

//-------------------------------------------------- Select --------------------------------------------------//

    public function select()
    {
        $sqlQuery = "SELECT * FROM " . $this->name_table;

        if ($this->id != null || $this->username != null) {
            $sqlQueryCond = "";

            if ($this->id != null) {
                $sqlQueryCond .= "id = :id";
            }
            if ($this->username != null) {
                if (!empty($sqlQueryCond)) {
                    $sqlQueryCond .= " OR ";
                }

                $sqlQueryCond .= "username = :username";
            }

            $sqlQuery .= " WHERE " . $sqlQueryCond . " ORDER BY id";

            try {
                $sqlStatement = $this->db->prepare($sqlQuery);

                if ($this->id != null) {
                    $sqlStatement->bindParam(':id', $this->id);
                }
                if ($this->username != null) {
                    $sqlStatement->bindParam(':username', $this->username);
                }

                $sqlStatement->execute();

                $result = $sqlStatement->fetch();

                if ($result) {
                    $this->id = $result['id'];
                    $this->username = $result['username'];
                    $this->password = $result['password'];
                    return $result;
                } else {
                    $this->id = 0;
                }
            } catch (PDOException $e) {
                $this->errorss[] = "contactez votre administrateur !! (select : user)";
                $this->errorss[] = $e->getMessage();
            }
        }
    }

//-------------------------------------------------- INSERT --------------------------------------------------//

    public function insert()
    {
        if ($this->username != null && $this->password != null) {
            $this->select();
            if ($this->id == 0) {
                $sqlQuery = "INSERT INTO " . $this->name_table . "(username, password) VALUES (:username, :password)";

                try {
                    $sqlStatement = $this->db->prepare($sqlQuery);
                    $sqlStatement->execute([
                        ":username" => $this->username,
                        ":password" => password_hash($this->password, PASSWORD_DEFAULT),
                    ]);
                    $this->id = $this->db->lastInsertId();
                } catch (PDOException $e) {
                    $this->errorss[] = "Enregistrement déjà existant";
                    $this->errorss[] = $e->getMessage();
                }
            } else {
                $this->id = 0;
                $this->errorss[] = "<h1 style='color : red'> Enregistrement déjà existant </h1><br>";
            }
        }
    }
}