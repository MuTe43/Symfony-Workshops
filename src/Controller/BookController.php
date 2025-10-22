<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/addBook', name: 'book_add')]
    public function addBook(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setPublished(true); // initialize published to true

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Increment nb_books of the selected author
            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);

            // Save book and updated author
            $em->persist($book);
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

        #[Route('/books', name: 'book_list')]
    public function listBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/list.html.twig', [
            'books' => $books,
        ]);
    }
}

