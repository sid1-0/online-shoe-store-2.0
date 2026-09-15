<?php
/**
 * Product - CRUD and queries for shoes, socks, and sneaker cleaner.
 * All products live in the `shoes` table and are separated by `category`.
 */
require_once __DIR__ . '/Database.php';

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /** @return array|null */
    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM shoes WHERE shoe_id = ?');
        $id = (int) $id;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** @return array */
    public function getAll()
    {
        $result = $this->db->query('SELECT * FROM shoes');
        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** @return array keyed by shoe_id */
    public function getAllMappedById()
    {
        $map = [];
        foreach ($this->getAll() as $row) {
            $map[$row['shoe_id']] = $row;
        }
        return $map;
    }

    /** @return array */
    public function getByCategory($category)
    {
        $stmt = $this->db->prepare('SELECT * FROM shoes WHERE category = ? ORDER BY name');
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** @return array */
    public function getByBrand($brand)
    {
        $stmt = $this->db->prepare('SELECT * FROM shoes WHERE brand = ? ORDER BY name');
        $stmt->bind_param('s', $brand);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** @return array */
    public function getByBrandAndCategory($brand, $category)
    {
        $stmt = $this->db->prepare(
            'SELECT shoe_id, name, brand, price, original_price, image_col, status, category, description, sub_category, sizes, created_at
             FROM shoes WHERE brand = ? AND category = ? ORDER BY name'
        );
        $stmt->bind_param('ss', $brand, $category);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Insert a new product.
     * @return int|false New shoe_id or false on failure
     */
    public function create($name, $brand, $price, $originalPrice, $description, $image, $status, $category, $sub_category, $sizes, $createdBy)
    {
        $createdAt = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO shoes (name, brand, price, original_price, description, image_col, status, category, sub_category, sizes, created_at, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $status = (int) $status;
        $createdBy = (int) $createdBy;
        $stmt->bind_param(
            'ssddssissssi',
            $name,
            $brand,
            $price,
            $originalPrice,
            $description,
            $image,
            $status,
            $category,
            $sub_category,
            $sizes,
            $createdAt,
            $createdBy
        );
        $ok = $stmt->execute();
        $newId = $ok ? $this->db->insert_id : false;
        $stmt->close();
        return $newId;
    }

    /** @return bool */
    public function update($id, $name, $brand, $price, $originalPrice, $description, $status, $image, $category, $sub_category, $sizes, $updatedBy)
    {
        $sql = 'UPDATE shoes SET name=?, brand=?, price=?, original_price=?, description=?, status=?, image_col=?, category=?, sub_category=?, sizes=?, updated_by=?, updated_at=NOW() WHERE shoe_id=?';
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $id = (int) $id;
        $updatedBy = (int) $updatedBy;
        $stmt->bind_param(
            'ssddsissssii',
            $name,
            $brand,
            $price,
            $originalPrice,
            $description,
            $status,
            $image,
            $category,
            $sub_category,
            $sizes,
            $updatedBy,
            $id
        );
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** @return bool */
    public function delete($id)
    {
        $id = (int) $id;
        $this->db->begin_transaction();
        try {
            $stmt1 = $this->db->prepare('DELETE FROM purchase_request WHERE shoe_id = ?');
            $stmt1->bind_param('i', $id);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->db->prepare('DELETE FROM shoes WHERE shoe_id = ?');
            $stmt2->bind_param('i', $id);
            $stmt2->execute();
            $stmt2->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getLastError()
    {
        return $this->db->error;
    }
}
