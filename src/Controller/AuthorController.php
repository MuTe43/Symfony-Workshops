<?php

namespace App\Controller;

use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\Request;

final class AuthorController extends AbstractController
{
    private $authors;
    public function __construct()
    {
        $this->authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpeg', 'username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );
    }

    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }


    #[Route('/list', name: 'author_list')]
    public function authorList(): Response
    {
        return $this->render('author/list.html.twig', [
            'authors' => $this->authors,
        ]);
    }

    //serach by id
    public function serchById($id): null|array
    {
        foreach ($this->authors as $author) {
            if ($author['id'] == $id) {
                return $author;
            }
        }
        return null;
    }

    #[Route('/authorDetails/{id}', name: 'author_authorDetails')]
    public function authorDetails($id): Response
    {
        $auth = $this->serchById($id);

        return $this->render('author/showAuthor.html.twig', [
            'auth' => $auth
        ]);
    }




    // Methode Add statique Author
    #[Route('/addAuthor', name: 'author_statique')]
    public function addAuthor(EntityManagerInterface $mr): Response
    {
        $author= new Author();
        $author->setUsername("wajdi");
        $author->setEmail("wajdistatique@test.com");
        if($author){
            $mr->persist($author);
            $mr->flush();
        }
        return $this->redirectToRoute("author_list");
    }

    #[Route('/getAuthors', name:'author_getFromDB')]
    public function getAuthors(AuthorRepository $authRepo):Response
    {
        $authors = $authRepo->findAll();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);

    }

    #[Route('/insert', name: 'author_insertAuthor')]
    public function insertAuthor(EntityManagerInterface $emi, Request $request)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $emi->persist($author);
            $emi->flush();
            return $this->redirectToRoute("author_getFromDB");
        }
        return $this->render('author/form.html.twig',[
            'authorForm' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'author_updateAuthor')]
    public function updateAuthor(EntityManagerInterface $emi, Request $request,$id): Response
    {
        $author = new Author();
        $author = $emi->getRepository(Author::class)->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $emi->persist($author);
            $emi->flush();
            return $this->redirectToRoute("author_getFromDB");
        }

        return $this->render('author/form.html.twig', [
            'authorForm' => $form,
        ]);
    }

    #[Route('/delete/{id}', name:"author_deleteAuthor")]
    public function deleteAuthor(EntityManagerInterface $emi, $id): Response
    {
        $author = $emi->getRepository(Author::class)->find($id);
        $emi->remove($author);
        $emi->flush();
        return $this->redirectToRoute("author_getFromDB");
    }

    #[Route('/authorsByEmail', name: 'author_list_by_email')]
    public function listAuthorsByEmail(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->listAuthorByEmail();

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

}