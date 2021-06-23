<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Entity\CommentCollect;
use App\Entity\Film;
use App\Form\CollectDeleteFormType;
use App\Form\CollectFormType;
use App\Form\CommentCollectFormType;
use App\Form\FilmFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/collection", name="collect_")
 */
class CollectController extends AbstractController
{

	/**
	 * Page controller to create a new collection
	 * @Route("/ajouter/", name="new")
	 * @Security ("is_granted('ROLE_USER')")
	 */
	public function createCollect(Request $request): Response
	{

		$newCollect = new Collect();
		$collectForm = $this->createForm(CollectFormType::class, $newCollect);
		$collectForm->handleRequest($request);

		// If no error and form submitted
		if ( $collectForm->isSubmitted() && $collectForm->isValid() )
		{

			// Hydrate the user with the missing data (not coming from the form)
			$newCollect
				->setPublicationDate(new \DateTime())
				->setAuthor($this->getUser());

			// Save the new collect in the DB via the entity manager
			$em = $this->getDoctrine()->getManager();
			$em->persist($newCollect);
			$em->flush();

			// Success flash message
			$this->addFlash('success', 'Votre collection a bien été créé.');
			return $this->redirectToRoute('collect_list');
		}

		return $this->render('collect/add.html.twig', ['collectForm' => $collectForm->createView(),]);
	}

	/**
	 * Controller for the collect list page
	 * @Route("/liste/", name="list")
	 */
	public function listCollect(Request $request, PaginatorInterface $paginator): Response
	{
		$requestedPage = $request->query->getInt('page', 1);
		// If requested page < 1 error 404
		if ( $requestedPage < 1 ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		// Create a query for paginator to get only the collections shown on the current page
		$query = $em->createQuery('SELECT c FROM App\Entity\Collect c ORDER BY c.publicationDate DESC');

		// Get the number of collection to show on each page from services.yaml
		$collectNumberByPage = $this->getParameter('collection_number_by_page');

		// On stock dans $articles le nombre d' articles de la page demandé dans l' URL
		$collects = $paginator->paginate($query, $requestedPage, $collectNumberByPage);

		return $this->render('collect/list.html.twig', ['collects' => $collects,]);
	}

	/**
	 * Controller for the collect view page
	 * @Route("/detail/{slug}/", name="detail")
	 */
	public function viewCollect(Request $request,Collect $collect): Response
	{
        // TODO: JS hover qui affiche le poster
        // TODO: récupérer la liste des films de la collection
        $em = $this->getDoctrine()->getManager();
        $filmRepo = $em->getRepository(Film::class);
        $films = $filmRepo->findAll();

	    //if user is not connected we call directly the view without processing the form below
        if (!$this->getUser()){

            return $this->render('collect/detail.html.twig',
                [
                    'collect' => $collect,
                    'films'   => $films,
                ]);
        }

        $comment = new CommentCollect();
        $commentForm = $this->createForm(CommentCollectFormType::class, $comment);
        $commentForm->handleRequest($request);

        //Verification that the form has been sent and has no errors
        if ($commentForm->isSubmitted() && $commentForm->isValid()){

            $comment
                ->setPublicationDate(new \DateTime())
                ->setCollect($collect)
                ->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire publié avec succès !');

            //Deletion of the two variables of the form and the newly created comment to avoid that the new form remains filled after the creation
            unset($comment);
            unset($commentForm);

            $comment = new CommentCollect();
            $commentForm = $this->createForm(CommentCollectFormType::class, $comment);
        }

        return $this->render('collect/detail.html.twig',
            [
                'collect' => $collect,
                'films'   => $films,
                'commentForm' => $commentForm->createView(),
            ]);
	}

    /**
     * Controller for the commentCollect deletion
     * @Route("/commentaire/supression/{id}", name="comment_delete")
     */
	public function deleteCommentCollect(CommentCollect $commentCollect, Request $request): Response
    {
        $tokenCSRF = $request->query->get('csrf_token');

        // Verify if token is valid
        if (!$this->isCsrfTokenValid('collect_comment_delete' . $commentCollect->getId(), $tokenCSRF) ){

            $this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer.');

        } else {

            // deletion of commnent
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentCollect);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire à bien été supprimer !');
        }

        return $this->redirectToRoute('collect_detail', [
           'slug' => $commentCollect->getCollect()->getSlug()
        ]);
    }

	/**
	 * Controller for the collect deletion
	 * @Route("/supprimer/{id}", name="delete")
	 */
	public function deleteCollect(Collect $collect, Request $request): Response
	{
		//TODO: add CSRF to avoid having possibility of going back to this page?

		// Redirects to 'collect_list' if user is not either ADMIN or the AUTHOR
		$user = $this->getUser();
		if ( $user->getRoles() != 'ROLE_ADMIN' && $user->getId() != $collect->getAuthor()->getId() )
		{
			return $this->redirectToRoute('collect_list');
		}

		// Creates form for display
		$collectDeleteForm = $this->createForm(CollectDeleteFormType::class);
		$collectDeleteForm->handleRequest($request);

		// If Cancel button is clicked
		if ( $collectDeleteForm->getClickedButton() === $collectDeleteForm->get('cancel') )
		{
			return $this->redirectToRoute('collect_list');
		}

		if ( $collectDeleteForm->isSubmitted() )
		{

			// Remove collect from DB
			$em = $this->getDoctrine()->getManager();
			$em->remove($collect);
			$em->flush();

			// Success flash message
			$this->addFlash('success', 'Collection supprimé.');
			return $this->redirectToRoute('collect_list');
		}


		return $this->render('collect/delete.html.twig',
			[
				'collectDeleteForm' => $collectDeleteForm->createView(),
				'collect'           => $collect,
			]);
	}

	/**
	 * Controller for the search page
	 * @Route("/rechercher/", name="search")
	 */
	public function search(Request $request): Response
	{
		return $this->render('collect/search.html.twig');
	}
}
