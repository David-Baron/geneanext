<?php

require_once(__DIR__ . '/../Engine/DatabaseConnection.php');

class UtilisateurModel extends DatabaseConnection
{
    public function findOneByCriteria(array $criteria)
    {
        $params = '';
        $i = 0;
        foreach ($criteria as $key => $value) {
            if ($i === 0) {
                $params .= "$key=:$key";
            } else {
                $params .= " AND $key=:$key";
            }
            $i++;
        }

        $sql = "SELECT * FROM utilisateurs WHERE $params";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($criteria);
        if ($user = $stmt->fetch()) {
            return $user;
        }
        return null;
    }

    public function insert(array $utilisateur)
    {
        $sql = "INSERT INTO utilisateurs (nom, motPasseUtil, Adresse, codeUtil, niveau) VALUES (:nom, :motPasseUtil, :Adresse, :codeUtil, :niveau)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($utilisateur);
        return $this->db->lastInsertId('utilisateurs');
    }
}
