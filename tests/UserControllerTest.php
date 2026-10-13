<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private function setUpUsers(): array
    {
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $userRepo = $em->getRepository(User::class);
        $admin = $userRepo->findOneBy(['email' => 'admin@shoes.com']);
        if (!$admin) {
            $admin = new User();
            $admin->setEmail('admin@shoes.com');
            $admin->setFirstName('Admin');
            $admin->setLastName('Super');
            $admin->setRoles(['ROLE_ADMIN', 'ROLE_EDITOR', 'ROLE_USER']);
            $admin->setPassword('testpass');
            $em->persist($admin);
        }

        $clientUser = $userRepo->findOneBy(['email' => 'client@shoes.com']);
        if (!$clientUser) {
            $clientUser = new User();
            $clientUser->setEmail('client@shoes.com');
            $clientUser->setFirstName('Client');
            $clientUser->setLastName('Martin');
            $clientUser->setRoles([]);
            $clientUser->setPassword('testpass');
            $em->persist($clientUser);
        }

        $em->flush();

        return [$admin, $clientUser];
    }

    public function testAnonymousAccessDeniedForAdminUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/user');

        $this->assertResponseRedirects('/login');
    }

    public function testAnonymousAccessDeniedForAdminCategory(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/category');

        $this->assertResponseRedirects('/login');
    }

    public function testAdminCanAccessAdminUser(): void
    {
        $client = static::createClient();
        [$admin] = $this->setUpUsers();

        $client->loginUser($admin);
        $client->request('GET', '/admin/user');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Utilisateurs');
    }

    public function testClientCannotAccessAdminUser(): void
    {
        $client = static::createClient();
        [, $clientUser] = $this->setUpUsers();

        $client->loginUser($clientUser);
        $client->request('GET', '/admin/user');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testRoleChangeAndWorkflow(): void
    {
        $client = static::createClient();
        [$admin, $clientUser] = $this->setUpUsers();
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $client->loginUser($admin);

        // Add editor role
        $client->request('GET', '/admin/user/' . $clientUser->getId() . '/add/editor');
        $this->assertResponseRedirects('/admin/user');

        $em->clear();
        $updatedUser = $em->getRepository(User::class)->find($clientUser->getId());
        $this->assertContains('ROLE_EDITOR', $updatedUser->getRoles());

        // Remove editor role
        $client->request('GET', '/admin/user/' . $clientUser->getId() . '/remove/editor');
        $this->assertResponseRedirects('/admin/user');

        $em->clear();
        $reupdatedUser = $em->getRepository(User::class)->find($clientUser->getId());
        $this->assertNotContains('ROLE_EDITOR', $reupdatedUser->getRoles());
    }

    public function testDeleteUser(): void
    {
        $client = static::createClient();
        [$admin] = $this->setUpUsers();
        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Create a temporary user to delete
        $tempUser = new User();
        $tempUser->setEmail('todelete@shoes.com');
        $tempUser->setFirstName('To');
        $tempUser->setLastName('Delete');
        $tempUser->setRoles([]);
        $tempUser->setPassword('pass');
        $em->persist($tempUser);
        $em->flush();

        $tempId = $tempUser->getId();

        $client->loginUser($admin);
        $client->request('GET', '/admin/user/' . $tempId . '/delete');
        $this->assertResponseRedirects('/admin/user');

        $em->clear();
        $deleted = $em->getRepository(User::class)->find($tempId);
        $this->assertNull($deleted);
    }
}
