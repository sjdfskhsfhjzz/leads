<?php
namespace App\Repository;
use App\Entity\Item;
use App\Entity\Order;
class OrderRepository
{
    /** @var \PDO */
    protected $pdo;
    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    //ОТСУТСВУЕТ ОБРАБОТКА ОШИБОК НА СЛУЧАЙ, ЕСЛИ ЧТО-ТО ПОЙДЕТ НЕ ТАК (try, catch)
    public function save(Order $order)
    {
        $sql = "INSERT INTO orders (id, sum, contractor_type) VALUES ({$order->id}, {$order->sum}, {$order->contractorType})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        foreach ($order->items as $item) {
            $sql = "INSERT INTO order_products (order_id,product_id,price,quantity) VALUES ({$order->id},{$item->getProductId()},{$item->getPrice()},{$item->getQuantity()})";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        }
    }

    //ОТСУТСВУЕТ ОБРАБОТКА ОШИБОК НА СЛУЧАЙ, ЕСЛИ ЧТО-ТО ПОЙДЕТ НЕ ТАК (try, catch)
    /** @return Order */
    public function get($orderId)
    {
        //НЕ КРИТИЧНО, НО ВОЗМОЖНО ЛУЧШЕ БУДЕТ ИСПОЛЬЗОВАТЬ ЛИБО ГОТОВОЕ РЕШЕНИЕ ДЛЯ ЗАПРОСОВ К БАЗЕ (например EntityManager в Symphony)
        $sql = "SELECT * FROM orders WHERE id={$orderId} LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $data = $stmt->fetch();
        $order = new Order($data['id']);
        $order->contractorType = $data['contractor_type']; //ЛУЧШЕ ВЫНЕСТИ КОНСТАНТЫ В ИНТЕРФЕЙС, КОТОРЫЙ СОЗДАТЬ ДЛЯ ЗАКАЗА
        $order->isPaid = $data['is_paid'];
        $order->sum = $data['sum'];
        $order->items = $this->getOrderItems($data['id']);
        return $order;
    }

    //ОТСУТСВУЕТ ОБРАБОТКА ОШИБОК НА СЛУЧАЙ, ЕСЛИ ЧТО-ТО ПОЙДЕТ НЕ ТАК (try, catch)
    /** @return Order[] */
    public function last($limit = 10)
    {
        $sql = "SELECT * FROM orders ORDER BY createdAt DESC LIMIT {$limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $orders = [];
        foreach ($data as $item) {
            $order = new Order($item['id']);
            $order->contractorType = $item['contractor_type'];
            $order->isPaid = $item['is_paid'];
            $order->sum = $item['sum'];
            $order->items = $this->getOrderItems($item['id']);
            $orders[] = $order;
        }
        return $orders;
    }

    //ЭТОТ МЕТОД ЛОГИЧНЕЙ БУДЕТ ПЕРЕНЕСТИ В КЛАСС \App\Entity\Order
    public function getOrderItems($orderId)
    {
        $sql = "SELECT * FROM order_products WHERE order_id={$orderId}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $items = [];
        foreach ($data as $item) {
            $items[] = new Item($item['order_id'], $item['product_id'],
                $item['price'], $item['quantity']);
        }
        return $items;
    }
}