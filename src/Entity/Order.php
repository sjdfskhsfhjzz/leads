<?php
namespace App\Entity;
use App\Service\BillGenerator;
use App\Service\BillMicroserviceClient;
const CONTRACTOR_TYPE_PERSON = 1;
const CONTRACTOR_TYPE_LEGAL = 2;


// ЯВНЫМ ОБРАЗОМ НАРУШАЮТСЯ ПРИНЦИПЫ SOLID - ПРАКТИЧЕСКИ ОТСУТСТВУЮТ АБСТРАКЦИИ/ИНТЕРФЕЙСЫ НАД СУЩНОСТЯМИ
class Order
{
    // ЗДЕСЬ НЕОБХОДИМО СОЗДАТЬ ЕЩЕ ОДНО ПОЛЕ ДЛЯ УНИКАЛЬНОГО НОМЕРА ЗАКАЗА ТИПА INCREMENT_ID.
    //И В НЕГО УЖЕ БУДЕТ СОХРАНЯТЬСЯ СГЕНЕРИРОВАННОЕ ЗНАЧЕНИЕ. А ПОЛЕ ID ОСТАВИТЬ ДЛЯ УНИКАЛЬНОЙ ИДЕНТИФИКАЦИИ ОБЪЕКТА ЗАКАЗА В БАЗЕ
    /** @var string */
    public $id;
    /** @var int */
    public $sum;
    /** @var Item[] */
    public $items = [];
    /** @var int */
    public $contractorType;
    /** @var bool */
    public $isPaid;
    /** @var BillGenerator */
    public $billGenerator;
    /** @var BillMicroserviceClient */
    public $billMicroserviceClient;
    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function getPayUrl()
    {
        return "http://some-pay-agregator.com/pay/" . $this->id; // ЭТО ЛУЧШЕ ВЫНЕСТИ В ОТДЕЛЬНЫЙ КЛАСС КОНФИГА. ИЛИ НА КРАЙНЯК, В КОНСТАНТУ
    }
    public function setBillGenerator($billGenerator)
    {
        $this->billGenerator = $billGenerator;
    }
    public function getBillUrl()
    {
        return $this->billGenerator->generate($this);
    }
    public function setBillClient(BillMicroserviceClient $cl)
    {
        $this->billMicroserviceClient = $cl;
    }
    public function isPaid() //ОТСУТСТВУЕТ ТИПИЗАЦИЯ (ОБЪЯВЛЕНИЕ ТИПОВ. В ДАННОМ СЛУЧАЕ ТИП ВОЗВРАЩАЕМОГО ЗНАЧЕНИЯ)
    {
        if ($this->contractorType == CONTRACTOR_TYPE_PERSON) {
            return $this->isPaid;
        }
        if ($this->contractorType == CONTRACTOR_TYPE_LEGAL) {
            return $this->billMicroserviceClient->IsPaid($this->id);
        }
    }

    //ВООБЩЕ В КЛАССЕ ПРИСУТСТВУЮТ МЕТОДЫ(getPayUrl(), setBillGenerator(), setBillClient()...), НЕ ОТНОСЯЩИЕСЯ НАПРЯМУЮ К ПОЛЯМ СУЩНОСТИ. ИХ, ПО-ХОРОШЕМУ, ВЫНЕСТИ В ОТДЕЛЬНЫЙ СЕРВИС (К ПРИМЕРУ) ИЛИ
    //В КРАЙНЕМ СЛУЧАЕ В РЕПОЗИТОРИЙ. А ЗДЕСЬ В ТЕКУЩЕМ КЛАССЕ ДОЛЖНЫ ПРЕДСТВАЛЕНЫ МЕТОДЫ (СЕТТЕРЫ И ГЕТТЕРЫ) ДЛЯ УСТАНОВКИ И ПОЛУЧЕНИЯ ЗНАЧЕНИЙ
    //ПОЛЕЙ СУЩНОСТИ ЗАКАЗА ORDER (НАПРИМЕР: getSum(), setSum(...), getId()....)
}