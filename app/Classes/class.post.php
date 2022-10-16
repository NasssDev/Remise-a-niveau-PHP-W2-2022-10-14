<?php
require_once "Fonctions/db.php";

class Post
{
    private $id;
    private $fk_user;
    private $text_post;

    private $name_table;

    private $db;
    private $erreurs = [];

    public function __construct($maconnexion = "", $name_table = "post")
    {
        $this->db = $maconnexion;
        $this->name_table = $name_table;
    }

    public function setNameTable($nameTable)
    {
        $this->name_table = $nameTable;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getErreurs()
    {
        return $this->erreurs;
    }

    public function setFkUser($fk_user)
    {
        $this->fk_user = $fk_user;
    }

    public function getFkUser()
    {
        return $this->fk_user;
    }

    public function getTextPost()
    {
        return $this->text_post;
    }

    public function setTextPost($text_post)
    {

        $this->text_post = $text_post;

    }

//------------------------- CRUD -------------------------//

    public function select()
    {

        $sqlQuery = "SELECT * FROM " . $this->name_table . " INNER JOIN users ON post.fk_user = users.id";

        if ($this->id != null || $this->fk_user != null) {
            $sqlQueryCond = "";
            if ($this->id != null) {
                $sqlQueryCond .= "id = :id";
            }
            if ($this->fk_user != null) {
                if (!empty($sqlQueryCond)) {
                    $sqlQueryCond .= " OR ";
                }
                $sqlQueryCond .= "users.id = :fk_user";
            }

            $sqlQuery .= " WHERE " . $sqlQueryCond;
        }

        $sqlQuery .= " ORDER BY post.id ";

        try {
            $sqlStatement = $this->db->prepare($sqlQuery);
            if ($this->id != null) {
                $sqlStatement->bindParam(':id', $this->id);
            }

            if ($this->fk_user != null) {
                $sqlStatement->bindParam(':fk_user', $this->fk_user);
            }

            $sqlStatement->execute();

            $results = $sqlStatement->fetchAll();

            if ($results != null) {
                return $results;
            }

        } catch (PDOException $e) {

            $this->erreurs[] = "Erreur ! contactez l'administrateur ! select :post";
            $this->erreurs[] = $e->getMessage();

        }
    }

    public function selectAll()
    {

        $sqlQuery = "SELECT users.username, users.role, post.* FROM users INNER JOIN post ON post.fk_user = users.id WHERE users.id = post.fk_user";

        try {
            $sqlStatement = $this->db->prepare($sqlQuery);
            $sqlStatement->execute();

            return $sqlStatement->fetchAll();

        } catch (PDOException $e) {
            throw $e;
        }

    }

    public function insert()
    {
        if ($this->fk_user != null) {

            $this->select();
            if ($this->text_post != null) {
                $sqlQuery = "INSERT INTO " . $this->name_table . " ( text_post, fk_user )
				VALUES (:text_post, :fk_user)";

                try {
                    $sqlStatement = $this->db->prepare($sqlQuery);
                    $sqlStatement->execute([
                        'text_post' => $this->text_post,
                        'fk_user' => $this->fk_user,
                    ]);

                } catch (PDOException $e) {

                    $this->erreurs[] = "Erreur ! contactez l'administrateur ! insert :post";
                    $this->erreurs[] = $e->getMessage();
                }
            }
        }
    }

    public function update()
    {

        if ($this->id != null) {

            $this->select();
            if ($this->text_post != null) {
                $sqlQuery = "UPDATE " . $this->name_table . " SET text_post = :text_post WHERE id = :id";

                try {
                    $sqlStatement = $this->db->prepare($sqlQuery);
                    $sqlStatement->execute([
                        ':id' => $this->id,
                        ':text_post' => $this->text_post,
                    ]);
                } catch (PDOException $e) {

                    $this->erreurs[] = "Erreur ! contactez l'administrateur ! update :post";
                    $this->erreurs[] = $e->getMessage();

                }
            }
        }
    }

    public function delete()
    {

        if ($this->id != null) {

            $this->select();

            if ($this->text_post != null) {

                try {
                    $sqlQuery = "DELETE FROM " . $this->name_table . " WHERE id = :id";
                    $sqlStatement = $this->db->prepare($sqlQuery);
                    $sqlStatement->execute([
                        ':id' => $this->id,
                    ]);
                } catch (PDOException $e) {

                    $this->erreurs[] = "Erreur ! contactez l'administrateur ! delete :post";
                    $this->erreurs[] = $e->getMessage();

                }
            }
        }
    }

}