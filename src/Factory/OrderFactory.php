<?php
namespace App\Factory;
use App\Entity\Item;
use App\Entity\Order;
class OrderFactory
{
    /** @var \PDO */
    protected $pdo;
    /**
     * OrderFactory constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    //ЭТОТ МЕТОД ЛОГИЧНЕЙ БУДЕТ ПЕРЕНЕСТИ В РЕПОЗИТОРИЙ, НАПРИМЕР
    public function generateOrderId()
    {
        $sql = "SELECT id FROM order1s ORDER BY createdAt DESC LIMIT 1";
        $result = $this->pdo->query($sql)->fetch();
        return (new \DateTime())->format("Y-m") . "-" . $result['id'] + 1;
    }
    public function createOrder($data, $id)
    {
        $order = new Order($id);

        //СЛЕДУЩИЙ КОД ДЛЯ ЧИСТОТЫ КОДА ЛУЧШЕ ВЫНЕСТИ В ОТДЕЛЬНУЮ ФУНКЦИЮ. К ПРИМЕРУ СОЗДАТЬ ФУНКЦИЮ setItems()
        foreach ($data as $key => $value)
        {
            if ($key == 'items')
            {
                foreach ($value as $itemValue) {
                    $order->items[] = new Item($id,
                        $itemValue['productId'], $itemValue['price'], $itemValue['quantity']);
                }
                continue;
            }
            $order->{$key} = $value;
        }
        return $order;

        //ОТСУТСВУЕТ ОБРАБОТКА ОШИБОК НА СЛУЧАЙ, ЕСЛИ ЧТО-ТО ПОЙДЕТ НЕ ТАК (try, catch)
    }
}