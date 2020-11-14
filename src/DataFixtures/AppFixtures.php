<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');

        $company = new Company();
        $company->setName($faker->company)
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setAddress($faker->streetAddress)
                ->setPostCode($faker->postcode)
                ->setCity($faker->city)
                ->setEmail($faker->companyEmail)
                ->setPhone($faker->e164PhoneNumber)
                ->setSiret('362 521 879 00034')
                ->setRcs('RCS PARIS B 517 403 572')
        ;
        $manager->persist($company);

        for($i = 0; $i < 50; $i++){
            $customer = new Customer();
            $customer->setFirstname($faker->firstName)
                    ->setLastname($faker->lastName)
                    ->setAddress($faker->streetAddress)
                    ->setPostCode($faker->postcode)
                    ->setCity($faker->city)
                    ->setEmail($faker->email)
                    ->setPhone($faker->e164PhoneNumber)
                    ->setCompany($company)
            ;
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
