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
        $book->setPublished(true); 

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);


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

        $publishedBooks = $bookRepository->findBy(['published' => true]);


        $publishedCount = $bookRepository->count(['published' => true]);
        $unpublishedCount = $bookRepository->count(['published' => false]);

        return $this->render('book/list.html.twig', [
            'books' => $publishedBooks,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);
    }


    #[Route('/book/edit/{id}', name: 'book_edit')]
    public function editBook(Request $request, EntityManagerInterface $em, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Pas besoin de persist, car l’objet existe déjà
            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    #[Route('/book/delete/{id}', name: 'book_delete')]
    public function deleteBook(Book $book, EntityManagerInterface $em): Response
    {
        $em->remove($book);
        $em->flush();

        $this->addFlash('success', 'Le livre a été supprimé avec succès.');
        return $this->redirectToRoute('book_list');
    }

    #[Route('/author/delete/empty', name: 'author_delete_empty')]
    public function deleteAuthorsWithNoBooks(AuthorRepository $authorRepository, EntityManagerInterface $em): Response
    {
        $authorsToDelete = $authorRepository->findBy(['nb_books' => 0]);

        foreach ($authorsToDelete as $author) {
            $em->remove($author);
        }

        $em->flush();

        $this->addFlash('info', 'Les auteurs sans livres ont été supprimés.');
        return $this->redirectToRoute('book_list');
    }

    #[Route('/detailsBook/{id}', name: 'book_detailsBook')]
    public function detailsBook(BookRepository $bookRepo, $id): Response
    {
        return $this->render('book/show.html.twig',[
            'book'=>$bookRepo->find($id),
        ]);
    }

    #[Route('/books/search', name: 'book_search', methods: ['GET'])]
    public function searchBook(Request $request, BookRepository $bookRepository): Response
    {
        $ref = $request->query->get('ref'); // récupère la valeur saisie dans l'input

        $book = null;
        if ($ref) {
            $book = $bookRepository->searchBookByRef($ref);
        }

        return $this->render('book/list.html.twig', [
            'books' => $book ? [$book] : $bookRepository->findBy(['published' => true]),
            'publishedCount' => $bookRepository->count(['published' => true]),
            'unpublishedCount' => $bookRepository->count(['published' => false]),
            'search_ref' => $ref,
        ]);
}

#[Route('/books/byAuthors', name: 'book_by_authors')]
public function booksListByAuthors(BookRepository $bookRepository): Response
{
    $books = $bookRepository->booksListByAuthors();

    return $this->render('book/list.html.twig', [
        'books' => $books,
        'publishedCount' => $bookRepository->count(['published' => true]),
        'unpublishedCount' => $bookRepository->count(['published' => false]),
    ]);
}

#[Route('/books/before2023', name: 'book_before2023')]
public function booksBefore2023(BookRepository $bookRepository): Response
{
    $books = $bookRepository->findBooksBefore2023ByAuthorsWithMoreThan10Books();

    return $this->render('book/list.html.twig', [
        'books' => $books,
        'publishedCount' => $bookRepository->count(['published' => true]),
        'unpublishedCount' => $bookRepository->count(['published' => false]),
    ]);
}

#[Route('/books/updateCategory', name: 'book_update_category')]
public function updateCategory(BookRepository $bookRepository): Response
{
    $updated = $bookRepository->updateCategorySciFiToRomance();

    $this->addFlash('success', "$updated livres ont été mis à jour de Science-Fiction vers Romance.");
    return $this->redirectToRoute('book_list');
}

}

