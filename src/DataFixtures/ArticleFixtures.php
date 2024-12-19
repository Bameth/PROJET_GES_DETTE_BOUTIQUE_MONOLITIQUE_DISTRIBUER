<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de 10 articles aléatoires
        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setLibelle("Article $i");
            $article->setQte(rand(1, 100));
            $article->setPrix(rand(500, 5000));
            // Persist l'article
            $manager->persist($article);
        }

        // Sauvegarde dans la base de données
        $manager->flush();

        dump('10 articles ont été créés et enregistrés en base.');
    }
}
