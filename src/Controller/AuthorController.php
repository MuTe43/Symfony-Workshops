<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}