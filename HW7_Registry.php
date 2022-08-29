<?php
//2. Найти в проекте паттерн Registry и объяснить, почему он был применён.


declare(strict_types = 1);


interface ISecurity
{
    /**
     * Получаем сущность пользователя по сессии
     *
     * @return Model\Entity\User|null
     */
    public function getUser(): ?Model\Entity\User;

    /**
     * Проверяет, является ли пользователь авторизованным
     *
     * @return bool
     */
    public function isLogged(): bool;

    /**
     * Производим операцию аутентификации
     *
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function authentication(string $login, string $password): bool;

    /**
     * Выход из системы
     *
     * @return void
     */
    public function logout(): void;
}


/* В компоненте Безопасности,
роль Interface: беспечить безопасность через декларирование типов данных

А в классе Security:
обеспечить доступ к параметрам  сессии, через геттер с получением пользователя по id  установкой сессии если id пользователя подтверждена
БД

 *
 */



class Security implements ISecurity
{
    private const SESSION_USER_IDENTITY = 'userId';

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getUser(): ?Model\Entity\User
    {
        $userId = $this->session->get(self::SESSION_USER_IDENTITY);

        return $userId ? (new Model\Repository\User())->getById($userId) : null;

    }
    //геттер с получением пользователя по id

    /**
     * @inheritdoc
     */
    public function isLogged(): bool
    {
        return $this->getUser() instanceof Model\Entity\User;
    }

    /**
     * @inheritdoc
     */
    public function authentication(string $login, string $password): bool
    {
        $user = $this->getUserRepository()->getByLogin($login);

        if ($user === null) {
            return false;
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            return false;
        }

        $this->session->set(self::SESSION_USER_IDENTITY, $user->getId());


        //установкой сессии если id пользователя подтверждена
        //БД




        return true;
    }

    /**
     * @inheritdoc
     */
    public function logout(): void
    {
        $this->session->set(self::SESSION_USER_IDENTITY, null);

        // здесь завершение сессии
    }

    /**
     * Фабричный метод для репозитория User
     *
     * @return Model\Repository\User
     */
    protected function getUserRepository(): Model\Repository\User
    {
        return new Model\Repository\User();
    }
}