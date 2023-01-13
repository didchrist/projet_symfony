<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;


class ArticuleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($j = 1; $j <= 3; $j++) {
            $category = new Category;
            $category->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());

            $manager->persist($category);


            for ($i = 1; $i <= 6; $i++) {
                $article = new Article();

                $content = '<p>';
                $content .= join('</p><p>', $faker->paragraphs(5));
                $content .= '</p>';


                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl(350, 150))
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);

                for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment;

                    $content = '<p>';
                    $content .= join('</p><p>', $faker->paragraphs(2));
                    $content .= '</p>';

                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-' . $days . 'days';

                    $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreateAt($faker->dateTimeBetween($minimum))
                        ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}