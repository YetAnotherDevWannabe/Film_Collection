<?php

namespace App\DataFixtures;

use App\Entity\Collect;
use App\Entity\Film;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
	// Nécessaire pour récupérer l' encodeur dans une variable
	private $encoder;

	// Nécessaire pour utiliser un service (ici UserPasswordEncoderInterface) depuis un autre service
	public function __construct(UserPasswordEncoderInterface $encoder)
	{
		$this->encoder = $encoder;
	}


	public function load(ObjectManager $manager)
	{
		/////////////
		/// USERS ///
		/////////////

		// User Admin
		$userAdmin = new User();
		$userAdmin
			->setEmail('admin@email.com')
			->setRoles(['ROLE_ADMIN'])
			->setPassword($this->encoder->encodePassword($userAdmin, 'Aa111111!'))
			->setNickname('admin')
			->setRegistrationDate(new \DateTime())
			->setActive(true)
		;
		$manager->persist($userAdmin);
		$users[] = $userAdmin;

		// User Q007
		$user = new User();
		$user
			->setEmail('q@q.q')
			->setRoles(['ROLE_USER'])
			->setPassword($this->encoder->encodePassword($user, 'Qq111111!'))
			->setNickname('Q007')
			->setRegistrationDate(new \DateTime())
			->setActive(true)
		;
		$manager->persist($user);
		$users[] = $user;


		/////////////
		/// FILMS ///
		/////////////

		// Wrath of man
		$newFilm = new Film();
		$newFilm
			->setTmdbId('637649')
			->setName('Wrath of man')
			->setYear(2021)
			->setSynopsis('A cold and mysterious new security guard for a Los Angeles cash truck company surprises his co-workers when he unleashes precision skills during a heist. The crew is left wondering who he is and where he came from. Soon, the marksman\'s ultimate motive becomes clear as he takes dramatic and irrevocable steps to settle a score.')
			->setGenre('Crime, Action')
			->setNote(78)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/o3thQMojEFxfeRWwH7UZmOqg8TS.jpg')
			->setTrailer('EFYEni2gsK0')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// A Quiet Place Part II
		$newFilm = new Film();
		$newFilm
			->setTmdbId('520763')
			->setName('A Quiet Place Part II')
			->setYear(2021)
			->setSynopsis('Following the events at home, the Abbott family now face the terrors of the outside world. Forced to venture into the unknown, they realize that the creatures that hunt by sound are not the only threats that lurk beyond the sand path.')
			->setGenre('Science Fiction, Thriller, Horror')
			->setNote(73)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/4q2hz2m8hubgvijz8Ez0T2Os2Yv.jpg')
			->setTrailer('XEMwSdne6UE')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Hitman's Wife's Bodyguard
		$newFilm = new Film();
		$newFilm
			->setTmdbId('522931')
			->setName('Hitman\'s Wife\'s Bodyguard')
			->setYear(2021)
			->setSynopsis('The world’s most lethal odd couple – bodyguard Michael Bryce and hitman Darius Kincaid – are back on another life-threatening mission. Still unlicensed and under scrutiny, Bryce is forced into action by Darius\'s even more volatile wife, the infamous international con artist Sonia Kincaid. As Bryce is driven over the edge by his two most dangerous protectees, the trio get in over their heads in a global plot and soon find that they are all that stand between Europe and a vengeful and powerful madman.')
			->setGenre('Action, Comedy, Thriller')
			->setNote(76)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/oht9CZHNDBEu5KitgxUsb0LhQ8v.jpg')
			->setTrailer('oZ2XyA28bQc')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Joker
		$newFilm = new Film();
		$newFilm
			->setTmdbId('475557')
			->setName('Joker')
			->setYear(2019)
			->setSynopsis('During the 1980s, a failed stand-up comedian is driven insane and turns to a life of crime and chaos in Gotham City while becoming an infamous psychopathic crime figure.')
			->setGenre('Crime, Thriller, Drama')
			->setNote(82)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/udDclJoHjfjb8Ekgsd4FDteOkCU.jpg')
			->setTrailer('t433PEQGErc')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Soul
		$newFilm = new Film();
		$newFilm
			->setTmdbId('508442')
			->setName('Soul')
			->setYear(2020)
			->setSynopsis('Joe Gardner is a middle school teacher with a love for jazz music. After a successful gig at the Half Note Club, he suddenly gets into an accident that separates his soul from his body and is transported to the You Seminar, a center in which souls develop and gain passions before being transported to a newborn child. Joe must enlist help from the other souls-in-training, like 22, a soul who has spent eons in the You Seminar, in order to get back to Earth')
			->setGenre('Family, Animation, Comedy, Drama, Music')
			->setNote(82)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/hm58Jw4Lw8OIeECIq5qyPYhAeRJ.jpg')
			->setTrailer('4TojlZYqPUo')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Tom Clancy's Without Remorse
		$newFilm = new Film();
		$newFilm
			->setTmdbId('567189')
			->setName('Tom Clancy\'s Without RemorseWithout Remorse')
			->setYear(2021)
			->setSynopsis('An elite Navy SEAL uncovers an international conspiracy while seeking justice for the murder of his pregnant wife.')
			->setGenre('Action, Thriller, War')
			->setNote(72)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/rEm96ib0sPiZBADNKBHKBv5bve9.jpg')
			->setTrailer('e-rw2cxFVLg')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Army of the Dead
		$newFilm = new Film();
		$newFilm
			->setTmdbId('503736')
			->setName('Army of the Dead')
			->setYear(2021)
			->setSynopsis('Following a zombie outbreak in Las Vegas, a group of mercenaries take the ultimate gamble: venturing into the quarantine zone to pull off the greatest heist ever attempted.')
			->setGenre(' Action, Crime, Science Fiction, Horror, Thriller ')
			->setNote(65)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/z8CExJekGrEThbpMXAmCFvvgoJR.jpg')
			->setTrailer('H83kjG5RCT8')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Raya and the Last Dragon
		$newFilm = new Film();
		$newFilm
			->setTmdbId('527774')
			->setName('Raya and the Last Dragon')
			->setYear(2021)
			->setSynopsis('Long ago, in the fantasy world of Kumandra, humans and dragons lived together in harmony. But when an evil force threatened the land, the dragons sacrificed themselves to save humanity. Now, 500 years later, that same evil has returned and it’s up to a lone warrior, Raya, to track down the legendary last dragon to restore the fractured land and its divided people.')
			->setGenre('Animation, Adventure, Fantasy, Family, Action')
			->setNote(81)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/lPsD10PP4rgUGiGR4CCXA6iY0QQ.jpg')
			->setTrailer('9BPMTr-NS9s')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Zack Snyder's Justice League
		$newFilm = new Film();
		$newFilm
			->setTmdbId('791373')
			->setName('Zack Snyder\'s Justice League')
			->setYear(2021)
			->setSynopsis('Determined to ensure Superman\'s ultimate sacrifice was not in vain, Bruce Wayne aligns forces with Diana Prince with plans to recruit a team of metahumans to protect the world from an approaching threat of catastrophic proportions.')
			->setGenre(' Action, Adventure, Fantasy, Science Fiction ')
			->setNote(84)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/tnAuB8q5vv7Ax9UAEje5Xi4BXik.jpg')
			->setTrailer('vM-Bja2Gy04')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Taxi
		$newFilm = new Film();
		$newFilm
			->setTmdbId('2330')
			->setName('Taxi')
			->setYear(1998)
			->setSynopsis('Daniel est un fou du volant. Cet ex-livreur de pizzas est aujourd\'hui chauffeur de taxi et sait échapper aux radars les plus perfectionnés. Pourtant, un jour, il croise la route d\'Emilien, policier recalé pour la huitième fois à son permis de conduire. Pour conserver son taxi, il accepte le marché que lui propose Emilien : l\'aider à démanteler un gang de braqueurs de banques qui écume les succursales de la ville à bord de puissants véhicules. Road-movie urbain sur un scénario de Luc Besson, également producteur.')
			->setGenre('Action, Comédie')
			->setNote(66)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/5d16tJ2DLImAwCY2w0LYZjgWZA1.jpg')
			->setTrailer('OnEcAcVPXUw')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Le Prénom
		$newFilm = new Film();
		$newFilm
			->setTmdbId('122198')
			->setName('Le Prénom')
			->setYear(2012)
			->setSynopsis('Vincent, la quarantaine triomphante, va être père pour la première fois. Invité à dîner chez Élisabeth et Pierre, sa sœur et son beau-frère, il y retrouve Claude, un ami d’enfance. En attendant l’arrivée d’Anna, sa jeune épouse éternellement en retard, on le presse de questions sur sa future paternité dans la bonne humeur générale... Mais quand on demande à Vincent s’il a déjà choisi un prénom pour l’enfant à naître, sa réponse plonge la famille dans le chaos.')
			->setGenre('Drame, Comédie')
			->setNote(77)
			->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/w5JWQo7ArqmoegKYiQbv8TeAJmg.jpg')
			->setTrailer('FX2ukwKgWlo')
			->setUser($userAdmin);
		$manager->persist($newFilm);

		// Star Wars: The Force Awakens
		$newFilm = new Film();
		$newFilm
			->setTmdbId('140607')
			->setName('Star Wars: The Force Awakens')
			->setYear(2015)
			->setSynopsis('Thirty years after defeating the Galactic Empire, Han Solo and his allies face a new threat from the evil Kylo Ren and his army of Stormtroopers.')
			->setGenre('Action, Adventure, Science Fiction, Fantasy')
			// ->setNote(73)
			// ->setPoster('https://www.themoviedb.org/t/p/w600_and_h900_bestv2/wqnLdwVXoBjKibFRR5U3y0aDUhs.jpg')
			// ->setTrailer('erLk59H86ww')
			->setUser($userAdmin);
		$manager->persist($newFilm);


		////////////////
		/// COLLECTS ///
		////////////////

		// Collect' Or
		$newCollect = new Collect();
		$newCollect
			->setName("Collect' Or")
			->setPublicationDate(new \DateTime())
			->setAuthor($userAdmin);
		$manager->persist($newCollect);

		// Collect' Trash
		$newCollect = new Collect();
		$newCollect
			->setName("Collect' Trash")
			->setPublicationDate(new \DateTime())
			->setAuthor($users[rand(0, count($users) - 1)]);
		$manager->persist($newCollect);

		// Collect' Taxe
		$newCollect = new Collect();
		$newCollect
			->setName("Collect' Taxe")
			->setPublicationDate(new \DateTime())
			->setAuthor($users[rand(0, count($users) - 1)]);
		$manager->persist($newCollect);

		// Collect' Les emmerdes
		$newCollect = new Collect();
		$newCollect
			->setName("Collect' Les emmerdes")
			->setPublicationDate(new \DateTime())
			->setAuthor($users[rand(0, count($users) - 1)]);
		$manager->persist($newCollect);

		// Collect' Tion
		$newCollect = new Collect();
		$newCollect
			->setName("Collect' Tion")
			->setPublicationDate(new \DateTime())
			->setAuthor($users[rand(0, count($users) - 1)]);
		$manager->persist($newCollect);

		for ($i = 0; $i < 8; $i++)
		{
			// Collect'
			$newCollect = new Collect();
			$newCollect
				->setName("Collect' " . (rand(0, 100) + $i))
				->setPublicationDate(new \DateTime())
				->setAuthor($users[rand(0, count($users) - 1)]);
			$manager->persist($newCollect);
		}

		// Save it to the DB
		$manager->flush();
	}
}
