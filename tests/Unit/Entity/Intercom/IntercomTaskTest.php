<?php
declare(strict_types=1);

namespace App\Tests\Unit\Entity\Intercom;

use App\Entity\Intercom\{ Status, Task, Type };
use App\Entity\User\User;
use PHPUnit\Framework\TestCase;

class IntercomTaskTest extends TestCase
{
    public function testTask(): void
    {
        $id = 1;
        $phone = '22332222333';
        $fullaname = 'Ivanov Ivano Ivanovich';
        $address = 'Adasko 2';
        $description = 'Something description';

        $status_id = 1;
        $status_name = Task::STATUS_COMPLETE;
        $status_description = 'Completed status description';
        $status = new Status();
        $status->setId($status_id);
        $status->setName($status_name);
        $status->setDescription($status_description);

        $type_id = 1;
        $type_name = 'abonent';
        $type_description = 'Abonent';
        $type = new Type();
        $type->setId($type_id);
        $type->setName($type_name);
        $type->setDescription($type_description);


        $mock_builder =  $this->getMockBuilder(User::class);
        $user = $mock_builder->getMock();

        $task = new Task($user);
        self::assertTrue($task->getUser() instanceof User);
        self::assertNull($task->getPhone());
        self::assertNull($task->getFullname());
        self::assertNull($task->getAddress());
        self::assertNull($task->getDescription());
        self::assertNull($task->getStatus());
        self::assertNull($task->getType());
        self::assertNull($task->getCompleted());

        $task->setId($id);
        $task->setPhone($phone);
        $task->setFullname($fullaname);
        $task->setAddress($address);
        $task->setDescription($description);
        $task->setStatus($status);

        self::assertEquals($id, $task->getId());
        self::assertTrue($status === $task->getStatus());
        self::assertTrue($task->getCompleted() instanceof \DateTime);
        self::assertEquals($phone, $task->getPhone());
        self::assertEquals($fullaname, $task->getFullname());
        self::assertEquals($address, $task->getAddress());
        self::assertFalse($task->isDeleted());
        $task->setDeleted(true);
        self::assertTrue($task->isDeleted());
        self::assertTrue($task->getCreated() instanceof \DateTime);
        self::assertEquals($fullaname, $task->__toString());
    }

    public function testTaskType(): void
    {
        $type_id = 1;
        $type_name = 'abonent';
        $type_description = 'Abonent';
        $type = new Type();
        $type->setId($type_id);
        $type->setName($type_name);
        $type->setDescription($type_description);

        self::assertEquals($type_id, $type->getId());
        self::assertEquals($type_name, $type->getName());
        self::assertEquals($type_description, $type->getDescription());
        self::assertEquals($type_description, $type->__toString());
    }

    public function testTaskStatus(): void
    {
        $status_id = 1;
        $status_name = Task::STATUS_COMPLETE;
        $status_description = 'Completed status description';
        $status = new Status();
        $status->setId($status_id);
        $status->setName($status_name);
        $status->setDescription($status_description);

        self::assertEquals($status_id, $status->getId());
        self::assertEquals($status_name, $status->getName());
        self::assertEquals($status_description, $status->getDescription());
        self::assertEquals($status_description, $status->__toString());
    }
}
