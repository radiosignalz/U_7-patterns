<?php







namespace Model\Repository;

//3. Добавить во все классы Repository использование паттерна Identity Map вместо постоянного
//    генерирования сущностей.





use Model\Entity;



namespace Model\Repository;



class Product
{
    /**
     * Поиск продуктов по массиву id
     *
     * @param int[] $ids
     * @return Entity\Product[]
     */
    public function search(array $ids = []): array
    {
        if (!count($ids)) {
            return [];
        }

        $productList = [];
        foreach ($this->getDataFromSource(['id' => $ids]) as $item) {
            $productList[] = new Entity\Product($item['id'], $item['name'], $item['price']);
        }

        return $productList;
    }

    /**
     * Получаем все продукты
     *
     * @return Entity\Product[]
     */
    public function fetchAll(): array
    {
        $productList = [];
        foreach ($this->getDataFromSource() as $item) {
            $productList[] = new Entity\Product($item['id'], $item['name'], $item['price']);
        }

        return $productList;
    }

    /**
     * Получаем продукты из источника данных
     *
     * @param array $search
     *
     * @return array
     */
    private function getDataFromSource(array $search = [])
    {
        $dataSource = [
            [
                'id' => 1,
                'name' => 'PHP',
                'price' => 15300,
            ],
            [
                'id' => 2,
                'name' => 'Python',
                'price' => 20400,
            ],
            [
                'id' => 3,
                'name' => 'C#',
                'price' => 30100,
            ],
            [
                'id' => 4,
                'name' => 'Java',
                'price' => 30600,
            ],
            [
                'id' => 5,
                'name' => 'Ruby',
                'price' => 18600,
            ],
            [
                'id' => 8,
                'name' => 'Delphi',
                'price' => 8400,
            ],
            [
                'id' => 9,
                'name' => 'C++',
                'price' => 19300,
            ],
            [
                'id' => 10,
                'name' => 'C',
                'price' => 12800,
            ],
            [
                'id' => 11,
                'name' => 'Lua',
                'price' => 5000,
            ],
        ];

        if (!count($search)) {
            return $dataSource;
        }

        $productFilter = function (array $dataSource) use ($search): bool {
            return in_array($dataSource[key($search)], current($search), true);
        };

        return array_filter($dataSource, $productFilter);
    }
}
class IdentityMap extends Product
{
    private $identityMap = [];

    public function add(Product $obj)
    {
        $key = $this->getGlobalKey(get_class($obj), $obj->search());
        $this->identityMap[$key] = $obj;
    }

    public function get(int $ids)
    {
        $key = $this->getGlobalKey($ids);
        if (isset($this->identityMap[$key])) {
            return $this->identityMap[$key];
        }
        throw new EmptyCacheException();
    }

    private function getGlobalKey(int $ids)
    {
        return sprintf('%s', $ids);
    }

//Использование Identity Map
    public function testIdentityMap(int $objectId)
    {
        $identityMap = new IdentityMap();
        try {
            return $identityMap->get($objectId);
        } catch (EmptyCacheException $e) {
        }
        $domainObject = $this->testIdentityMap($objectId)->getEntityById(
            $objectId);
        $identityMap->add($domainObject);
        return $domainObject;
    }
}
