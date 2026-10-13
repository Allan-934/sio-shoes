<?php

namespace App\Tests;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubCategoryControllerTest extends WebTestCase
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

    public function testAnonymousAccessDeniedForSubCategory(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sub/category');

        $this->assertResponseRedirects('/login');
    }

    public function testNonAdminAccessDeniedForSubCategory(): void
    {
        $client = static::createClient();
        [$admin, $clientUser] = $this->setUpUsers();

        $client->loginUser($clientUser);
        $client->request('GET', '/sub/category');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminCanAccessSubCategoryIndex(): void
    {
        $client = static::createClient();
        [$admin, $clientUser] = $this->setUpUsers();

        $client->loginUser($admin);
        $client->request('GET', '/sub/category');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sous-catégories');
    }
}
