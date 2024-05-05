<?php

use PHPUnit\Framework\TestCase;
use App\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    protected $userRepository;

    protected function setUp(): void
    {
        $managerRegistry = $this->getMockBuilder(\Doctrine\Persistence\ManagerRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRepository = new UserRepository($managerRegistry);
    }

    // Test getUsers method
    public function testGetUsers()
    {
        $batchId = 1;
        $filter = 'test';
        $orderBy = 'username';
        $order = 'ASC';

        // Mock the query builder
        $queryBuilder = $this->getMockBuilder(\Doctrine\ORM\QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->once())
            ->method('select')
            ->with($this->equalTo('p.username, p.email'))
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($this->equalTo("p.batch_id = :batchId"))
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with($this->equalTo('batchId'), $this->equalTo($batchId))
            ->willReturnSelf();

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with($this->equalTo("p.$orderBy"), $this->equalTo($order))
            ->willReturnSelf();

        $query = $this->getMockBuilder(\Doctrine\ORM\Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query->expects($this->once())
            ->method('execute')
            ->willReturn([
                ['username' => 'admin', 'email' => 'admin@example.com'],
                ['username' => 'user', 'email' => 'user@example.com']
            ]);

        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $this->userRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with($this->equalTo('p'))
            ->willReturn($queryBuilder);

        $result = $this->userRepository->getUsers($batchId, $filter, $orderBy, $order);

        // Assert that the result matches the expected usernames and emails
        $expectedResult = [
            ['username' => 'admin', 'email' => 'admin@example.com'],
            ['username' => 'user', 'email' => 'user@example.com']
        ];
        $this->assertEquals($expectedResult, $result);
    }
}
