<?php

namespace SumoCoders\FrameworkMultiUserBundle\Tests\Command;

use PHPUnit_Framework_TestCase;
use SumoCoders\FrameworkMultiUserBundle\Command\DeleteUserHandler;
use SumoCoders\FrameworkMultiUserBundle\DataTransferObject\UserDataTransferObject;
use SumoCoders\FrameworkMultiUserBundle\User\InMemoryUserRepository;
use SumoCoders\FrameworkMultiUserBundle\User\Interfaces\UserRepository;
use SumoCoders\FrameworkMultiUserBundle\User\UserRepositoryCollection;

class DeleteUserHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var UserRepository */
    private $userRepository;

    /** @var UserRepositoryCollection */
    private $userRepositoryCollection;

    public function setUp()
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->userRepositoryCollection = new UserRepositoryCollection([$this->userRepository]);
    }

    /**
     * Test if DeleteUserHandler gets handled.
     */
    public function testUpdateUserGetsHandled()
    {
        $handler = new DeleteUserHandler($this->userRepositoryCollection);

        $deletingUser = $this->userRepository->findByUsername('wouter');

        $handler->handle(UserDataTransferObject::fromUser($deletingUser));

        $this->assertNotNull($deletingUser);
        $this->assertNull(
            $this->userRepository->findByUsername('wouter')
        );
    }
}
