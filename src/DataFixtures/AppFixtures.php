<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\Category;
use App\Entity\Contributor;

use App\Repository\UserRepository;
use App\Repository\ContributorRepository;


class AppFixtures extends Fixture
{

    /**
     * Encodeur de mot de passe
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var ContributorRepository
     */
    private $contributorRepo;

    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(UserPasswordEncoderInterface $encoder, ContributorRepository $contributorRepo, UserRepository $userRepo)
    {
        $this->encoder = $encoder;
        $this->contributorRepo = $contributorRepo;
        $this->userRepo = $userRepo;
    }

    public function load(ObjectManager $manager)
    {
       $worksIt = ['Dev web fullstack', 'Dev web frontend', 'Dev web Backend', 'UX Designer', 'Admin BDD', ' Dev Android', 'Dev iOS', 'Dev fullstack', 'Dev Python'];
        // $product = new Product();
        // $manager->persist($product);
        $levelProject  = ['dev', 'prod'];


        $faker = Factory::create('fr_FR');

        for ($i=1; $i <= 20; $i++) {
          $user = new User();
          $user->setEmail($faker->email)
               ->setPassword($this->encoder->encodePassword($user, 'password'))
               ->setFullname($faker->name)
               ->setRoles(["ROLE_USER"])
               ->setPhoto("user.jpg")
;
          $manager->persist($user);
        }

        $admin = new User();
        $admin->setEmail('admin@gmail.com')
             ->setPassword($this->encoder->encodePassword($user, 'admin'))
             ->setFullname("Omera Admin")
             ->setRoles(["ROLE_ADMIN"])
             ->setPhoto("user.jpg")
;
        $manager->persist($admin);
        $manager->flush();

        for ($i=1; $i <= 10; $i++) {
          $contributor = (new Contributor())
                          ->setFullname($faker->name)
                          ->setEmail($faker->email)
                          ->setProfession($worksIt[array_rand($worksIt, 1)])
                          ->setDescription($faker->text)
                          ->setPhoto('dev.jpg')
                          ->setIsMember(true)
                          ->setCreatedAt($faker->dateTimeBetween('-6 months'));
          $manager->persist($contributor);
        }
        $manager->flush();
        $categories = ['Creative', 'Fresh', 'Innovative', 'Inspiring', 'New Trends'];
        $authors = $this->contributorRepo->findAll();
        $user = $this->userRepo->findAll();
        for ($i=0; $i < 5; $i++) {
          $category = (new Category())->setName($categories[$i]);
          $manager->persist($category);
          for ($u=1; $u <= 20; $u++) {
            $picture = (new Image())->setUrl("post.jpg");
            $article = (new Article())
                        ->setTitle($faker->sentence)
                        ->setSlug($faker->slug)
                        ->setContent(join($faker->paragraphs(6), ' '))
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->addCategory($category)
                        ->addAuthor($authors[mt_rand(0, count($authors)-1)]);
            $picture->setArticle($article);
            $article->addPicture($picture);
            $manager->persist($article);

            for ($w=1; $w <= 10; $w++) {
              $comment = (new Comment())
                          ->setAuthor($user[mt_rand(0, count($user)-1)])
                          ->setArticle($article)
                          ->setContent($faker->text);
              $now = new \DateTime();
              $interval = $now->diff($article->getCreatedAt());
              $days = $interval->days;
              $minimum = '-'.$days.' days';
              $comment->setCreatedAt($faker->dateTimeBetween($minimum));
              $manager->persist($comment);
            }

            for ($w=0; $w < 10; $w++) {
              $pictureProject = (new Image())->setUrl("project.jpg");
              $project = (new Project())
                          ->setCustomer($faker->name)
                          ->setEmail($faker->email)
                          ->setSubject($faker->sentence)
                          ->setBudget(mt_rand(50000, 10000000))
                          ->addCategory($category)
                          ->setChallenge(join($faker->paragraphs(2), ' '))
                          ->setSolution(join($faker->paragraphs(3), ' '))
                          ->setStatus($levelProject[mt_rand(0, 1)])
                          ->setSatisfiedCustomer(true)
                          ->setCreatedAt($faker->dateTimeBetween('-10 months'));
              $pictureProject->setProject($project);
              $project->addImage($pictureProject);
              $manager->persist($project);
            }
          }

        }


        $manager->flush();
    }
}
