<?php

namespace App\Model;

use App\Constructor;

/**
 * class MessageModel
 * request to database about messages
 */
class MessageModel extends Constructor
{
    public function getMessages(array $hash): array
    {
        $req = $this->db->prepare('SELECT owner, message FROM message WHERE id_user1 = ? AND id_user2 = ? ORDER BY date ASC LIMIT 100');
        $req->execute($hash);

        return $req->fetchAll();
    }

    public function getAllMessages(): array
    {
        $req = $this->db->prepare('SELECT * FROM message WHERE id_user1 = ? OR id_user2 = ? ORDER BY date ASC');
        $req->execute([$_SESSION['id'], $_SESSION['id']]);

        return $req->fetchAll();
    }

    public function setMessage(array $hash): array
    {
        $req = $this->db->prepare('INSERT INTO message VALUES (?, ?, ?, ?, ?)');
        $req->execute($hash);
    }

    public function delAllMessages(int $id): bool
    {
        $req = $this->db->prepare('DELETE FROM message WHERE id_user1 = ? OR id_user2 = ?');

        return $req->execute([$id, $id]);
    }
}
